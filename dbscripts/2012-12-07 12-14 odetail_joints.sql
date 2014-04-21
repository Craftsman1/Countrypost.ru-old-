ALTER TABLE `odetail_joints` DROP `odetail_joint_cost_usd`;

ALTER TABLE  `odetail_joints` CHANGE  `odetail_joint_count`  `odetail_joint_count` INT( 11 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `odetail_joints` CHANGE  `odetail_joint_id`  `joint_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE  `odetail_joint_cost`  `cost` INT( 11 ) NOT NULL DEFAULT  '0',
CHANGE  `odetail_joint_count`  `count` INT( 11 ) NOT NULL DEFAULT  '0';