-- на вход скрипту подаетс€ код страны партнеров: 5 =  итай, 6 =  оре€
-- пример вызова скрипта: CALL relocate_clients(5)
DROP PROCEDURE IF EXISTS relocate_clients//

CREATE PROCEDURE relocate_clients(IN country_id INT)
BEGIN
	DECLARE 
		client_count, 
		manager_count, 
		avg_client_count, 
		i, 
		manager_from, 
		manager_to, 
		client_id INT;
	
	-- 1. считаем всех клиентов в стране
	SET client_count = 
		(SELECT COUNT(clients.client_user)
		FROM clients
			INNER JOIN users
				ON users.user_id = clients.client_user
			INNER JOIN c2m
				ON c2m.client_id = users.user_id
			INNER JOIN managers
				ON managers.manager_user = c2m.manager_id
		WHERE 
			managers.manager_status = 1 AND 
			managers.manager_country = country_id);
		
	-- 2. считаем всех партнеров в стране
	SET manager_count = 
		(SELECT COUNT(managers.manager_user)
		FROM managers
		WHERE 
			managers.manager_status = 1 AND 
			managers.manager_country = country_id);

	-- 3. считаем среднее число клиентов
	SET avg_client_count = CEIL(client_count / manager_count);

	-- 4. цикл по св€з€м клиентов и партнеров
	SET i = 0;

	client_loop:
	WHILE i <= client_count DO
		SET i = i + 1;

		-- 4.1 находим партнера с лишними клиентами
		SET manager_from = 
			(SELECT MIN(T.manager_id)
			FROM
				(SELECT DISTINCT c2m.manager_id
				FROM c2m
					INNER JOIN managers
						ON managers.manager_user = c2m.manager_id
					INNER JOIN users
						ON users.user_id = c2m.client_id
				WHERE 
					managers.manager_status = 1 AND 
					managers.manager_country = country_id
				GROUP BY 
					c2m.manager_id
				HAVING
					COUNT(c2m.manager_id) >= avg_client_count)
			AS T);
			
		IF (ISNULL(manager_from)) THEN 
			LEAVE client_loop;
		END IF;
		
		-- 4.2 находим его последнего клиента
		SET client_id = 
			(SELECT MAX(c2m.client_id)
			FROM c2m
				INNER JOIN users
					ON users.user_id = c2m.client_id
			WHERE 
				c2m.manager_id = manager_from);
			
		IF (ISNULL(client_id)) THEN 
			LEAVE client_loop;
		END IF;
		
		-- 4.3 находим партнера с недостатком клиентов, у таких может не быть клиентов вообще
		SET manager_to = 
			(SELECT MIN(managers.manager_user)
			FROM managers
				LEFT JOIN c2m
					ON c2m.manager_id = managers.manager_user
			WHERE 
				managers.manager_status = 1 AND 
				managers.manager_country = country_id AND
				ISNULL(c2m.manager_id));

		IF (ISNULL(manager_to)) THEN 
			SET manager_to = 
				(SELECT MIN(T.manager_id)
				FROM
					(SELECT DISTINCT c2m.manager_id
					FROM c2m
						INNER JOIN managers
							ON managers.manager_user = c2m.manager_id
						INNER JOIN users
							ON users.user_id = c2m.client_id
					WHERE 
						managers.manager_status = 1 AND 
						managers.manager_country = country_id
					GROUP BY 
						c2m.manager_id
					HAVING
						COUNT(c2m.manager_id) < avg_client_count)
				AS T);
		END IF;
		
		IF (ISNULL(manager_to)) THEN 
			LEAVE client_loop;
		END IF;
			
		-- 4.5 прив€зываем клиента к новому партнеру
		UPDATE c2m
		SET 
			c2m.manager_id = manager_to
		WHERE
			c2m.manager_id = manager_from AND
			c2m.client_id = client_id;
			
	END WHILE;
END//

-- на вход скрипту подаетс€ код страны партнеров: 5 =  итай, 6 =  оре€
-- пример вызова скрипта: CALL patch_client_orders_and_packages(5)
DROP PROCEDURE IF EXISTS patch_client_orders_and_packages//

CREATE PROCEDURE patch_client_orders_and_packages(IN country_id INT)
BEGIN
	DECLARE 
		client_count, 
		manager, 
		i, 
		client INT;
	DECLARE client_cursor CURSOR FOR SELECT clients.client_user FROM shipito.clients ORDER BY clients.client_user;
	
	-- 1. считаем всех клиентов в стране
	SET client_count = 
		(SELECT COUNT(clients.client_user)
		FROM clients
			INNER JOIN users
				ON users.user_id = clients.client_user
			INNER JOIN c2m
				ON c2m.client_id = users.user_id
			INNER JOIN managers
				ON managers.manager_user = c2m.manager_id
		WHERE 
			managers.manager_status = 1 AND 
			managers.manager_country = country_id);
		
	-- 2. цикл по св€з€м клиентов и партнеров
	SET i = 0;
	OPEN client_cursor;

	client_loop:
	WHILE i < client_count DO
		-- 2.1 находим клиента
		FETCH client_cursor INTO client;

		IF (ISNULL(client)) THEN 
			LEAVE client_loop;
		END IF;
		
		-- 2.2 находим партнера
		SET manager = 
			(SELECT DISTINCT c2m.manager_id
			FROM c2m
				INNER JOIN managers
					ON managers.manager_user = c2m.manager_id
			WHERE 
				c2m.client_id = client AND 
				managers.manager_status = 1 AND 
				managers.manager_country = country_id);
			
		IF (ISNULL(manager)) THEN 
			LEAVE client_loop;
		END IF;
		
		-- 2.3 патчим посылки
		UPDATE packages
		SET packages.package_manager = manager
		WHERE 
			packages.package_client = client AND 
			packages.package_manager <> manager;
			
		-- 2.4 патчим товары посылок
		UPDATE pdetails
		SET pdetails.pdetail_manager = manager
		WHERE 
			pdetails.pdetail_client = client AND 
			pdetails.pdetail_manager <> manager;
			
		-- 2.5 патчим заказы
		UPDATE orders
		SET orders.order_manager = manager
		WHERE 
			orders.order_client = client AND 
			orders.order_manager <> manager;
			
		-- 2.6 патчим товары заказов
		UPDATE odetails
		SET odetails.odetail_manager = manager
		WHERE 
			odetails.odetail_client = client AND 
			odetails.odetail_manager <> manager;
			
		SET i = i + 1;
	END WHILE;

	CLOSE client_cursor;
END//