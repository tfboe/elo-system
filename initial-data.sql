INSERT INTO `elo_users` (`id`, `email`, `password`, `remember_token`, `jwt_version`, `confirmed_a_g_b_version`, `created_at`, `updated_at`, `activated`, `admin`) VALUES
('b7647141-bdd5-4603-9485-e8b6784c6f80', 'dev@dev.at', '$2y$10$WkVs2qvi9CvQXjIqr9OLSuE537/MM5CKhli45siz96mXUqMVc669a', NULL, 1, 0, '2021-04-26 06:36:20', '2021-04-26 06:36:20', 1, 0);
INSERT INTO `elo_rankingSystems` (`id`, `service_name`, `default_for_level`, `generation_interval`, `sub_class_data`, `created_at`, `updated_at`, `name`, `open_sync_from`) VALUES
('d08eda56-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:22:55', 'Open Single', NULL),
('dc264cb5-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:22:59', 'Open Double', NULL),
('e6456a1e-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:02', 'Women Single', NULL),
('e8fc01a0-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:03', 'Women Double', NULL),
('edba2a8b-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:04', 'Junior Single', NULL),
('f06e92ce-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:05', 'Junior Double', NULL),
('f4f95737-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:05', 'Senior Single', NULL),
('f7972e41-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:06', 'Senior Double', NULL),
('fbb021e4-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:06', 'Classic', NULL),
('fe9448f4-a7ee-11eb-8243-0242ac140002', 'EloRanking', NULL, 1, '{}', '2021-04-28 06:52:55', '2021-04-28 07:23:06', 'Mixed', NULL);
