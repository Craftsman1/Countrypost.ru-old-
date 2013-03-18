ALTER TABLE  `bids` ADD  `manager_tax_percentage` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `manager_tax`;
ALTER TABLE  `bids` ADD  `foto_tax_percentage` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `foto_tax`;
ALTER TABLE  `bids` ADD  `requested_foto_count` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `foto_tax_percentage`;