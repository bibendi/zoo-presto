INSERT INTO `PREFIX_tax` (`id_tax`, `rate`, `active`) VALUES (177, '18.000', 1);
INSERT INTO `PREFIX_tax_lang` (`id_tax`, `id_lang`, `name`) VALUES (177, ID_LNG, 'НДС 18.0%');
INSERT INTO `PREFIX_tax_rule` (`id_tax_rules_group`, `id_country`, `id_state`, `id_tax`, `state_behavior`) VALUES (177, 177, 0, 177, 0);
INSERT INTO `PREFIX_tax_rules_group` (`id_tax_rules_group`, `name`, `active`) VALUES (177, 'НДС 18.0%', 1);