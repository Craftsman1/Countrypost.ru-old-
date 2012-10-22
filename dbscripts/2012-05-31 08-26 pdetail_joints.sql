ALTER TABLE  `pdetail_joints` ADD  `package_id` INT( 11 ) NOT NULL ,
ADD  `pdetail_foto_request` INT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `pdetail_joints` DROP  `pdetail_joint_cost` ,
DROP  `pdetail_joint_cost_usd` ,
DROP  `pdetail_joint_count` ;

ALTER TABLE  `pdetail_joints` ADD  `pdetail_joint_count` INT NOT NULL DEFAULT  '0';
