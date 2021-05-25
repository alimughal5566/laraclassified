
DROP TABLE IF EXISTS `<<prefix>>reviews`;

ALTER TABLE `<<prefix>>posts`
  DROP `rating_cache`,
  DROP `rating_count`;
