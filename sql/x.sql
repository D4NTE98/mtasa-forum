--
-- Struktura tabeli dla tabeli `forum_categories`
--

CREATE TABLE `forum_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(140) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT 'fa-folder',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `section_id`, `name`, `description`, `icon`, `sort_order`) VALUES
(1, 1, 'Ogłoszenia', 'Informacje na temat projektu', 'fa-bullhorn', 10),
(2, 1, 'Regulaminy', 'Obowiązujące zasady', 'fa-file-lines', 20),
(3, 1, 'Propozycje', 'Masz jakiś pomysł?', 'fa-lightbulb', 30),
(4, 1, 'Błędy', 'Znalazłeś błąd? Zgłoś go!', 'fa-triangle-exclamation', 40),
(5, 1, 'Skargi', 'Tutaj napiszesz skargę', 'fa-flag', 50),
(6, 2, 'Skład administracji', 'Aktualny skład administracji', 'fa-users', 10),
(7, 2, 'Rotacje w administracji', 'Wszelkie zmiany rangi tutaj', 'fa-headset', 20),
(8, 2, 'Rekrutacje', 'Tutaj złożysz swoje podanie', 'fa-user-gear', 30),
(9, 3, 'San Andreas County Sheriff\'s Department', 'Departament', 'fa-user-tie', 10),
(10, 4, 'Informacje dot. org.', 'Wszelkie info o organizacjach', 'fa-circle-info', 10),
(11, 5, 'Kosz', 'Tutaj są ukryte tematy', 'fa-trash', 10),
(12, 5, 'Przedstaw się!', 'Jesteś tutaj nowy?', 'fa-hand', 20);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `forum_groups`
--

CREATE TABLE `forum_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#222222',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `permission_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_groups`
--

INSERT INTO `forum_groups` (`id`, `name`, `color`, `icon`, `permission_level`) VALUES
(1, 'Użytkownik', '#2a2a2a', 'fa-user', 1),
(2, 'Moderator', '#2f7dd1', 'fa-shield-halved', 5),
(3, 'Administrator', '#d12f2f', 'fa-crown', 9);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `topic_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `content` mediumtext NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `topic_id`, `user_id`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'witam witam', '2026-03-01 14:32:41', '2026-03-01 14:32:41');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `forum_sections`
--

CREATE TABLE `forum_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_sections`
--

INSERT INTO `forum_sections` (`id`, `name`, `sort_order`) VALUES
(1, 'FORUM', 10),
(2, 'ADMINISTRACJA', 20),
(3, 'FRAKCJE', 30),
(4, 'ORGANIZACJE', 40),
(5, 'OFF-TOPIC', 50);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `replies_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `category_id`, `user_id`, `title`, `created_at`, `updated_at`, `views`, `replies_count`, `is_pinned`, `is_locked`) VALUES
(1, 12, 1, 'elo', '2026-03-01 14:32:41', '2026-03-01 14:32:41', 4, 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `forum_users`
--

CREATE TABLE `forum_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `last_seen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `forum_users`
--

INSERT INTO `forum_users` (`id`, `username`, `email`, `password_hash`, `group_id`, `created_at`, `last_seen`) VALUES
(1, 'D4NTE', 'test@test.pl', '$2y$10$fskjmbcZfO/M59sJaACD1elIE8qDW2uGvi6nErljsy.twSuZtGkXK', 3, '2026-03-01 14:32:22', '2026-03-01 14:42:25');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section` (`section_id`,`sort_order`);

--
-- Indeksy dla tabeli `forum_groups`
--
ALTER TABLE `forum_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_perm` (`permission_level`);

--
-- Indeksy dla tabeli `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_topic_created` (`topic_id`,`created_at`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indeksy dla tabeli `forum_sections`
--
ALTER TABLE `forum_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indeksy dla tabeli `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cat_updated` (`category_id`,`is_pinned`,`updated_at`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indeksy dla tabeli `forum_users`
--
ALTER TABLE `forum_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD UNIQUE KEY `uq_email` (`email`),
  ADD KEY `idx_group` (`group_id`),
  ADD KEY `idx_last_seen` (`last_seen`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `forum_groups`
--
ALTER TABLE `forum_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `forum_sections`
--
ALTER TABLE `forum_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `forum_users`
--
ALTER TABLE `forum_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD CONSTRAINT `fk_cat_section` FOREIGN KEY (`section_id`) REFERENCES `forum_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `fk_posts_topic` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `forum_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `fk_topics_cat` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_topics_user` FOREIGN KEY (`user_id`) REFERENCES `forum_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `forum_users`
--
ALTER TABLE `forum_users`
  ADD CONSTRAINT `fk_users_group` FOREIGN KEY (`group_id`) REFERENCES `forum_groups` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
