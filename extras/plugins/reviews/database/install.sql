
DROP TABLE IF EXISTS `<<prefix>>reviews`;
CREATE TABLE `<<prefix>>reviews` (
  `id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `approved` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `spam` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `<<prefix>>reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `<<prefix>>reviews`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `<<prefix>>posts` ADD `rating_cache` float(2,1) unsigned NOT NULL DEFAULT '0.0' AFTER `visits`;
ALTER TABLE `<<prefix>>posts` ADD `rating_count` int(11) unsigned NOT NULL DEFAULT '0' AFTER `rating_cache`;