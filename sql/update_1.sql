INSERT INTO `ruolo` (`id`, `ruolo`) VALUES
(1, 'admin'),
(2, 'studente'),
(3, 'docente');

INSERT INTO `ruolo_users` (`id_ruolo`, `id_user`) VALUES
(1, 2),
(2, 1);