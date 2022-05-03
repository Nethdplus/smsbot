DROP TABLE IF EXISTS `codigos`;
CREATE TABLE `codigos` (
  `id` INTEGER PRIMARY KEY,
  `codigo` varchar(100) NOT NULL UNIQUE,
  `data` varchar(100) NOT NULL,
  `valor` float NOT NULL
);


DROP TABLE IF EXISTS `resgates`;
CREATE TABLE `resgates` (
  `id` INTEGER PRIMARY KEY,
  `id_telegram` bigint(20) NOT NULL,
  `id_resgate` varchar(100) NOT NULL UNIQUE,
  `saldo_resgate` float NOT NULL,
  `data_resgate` varchar(100) NOT NULL
);


DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INTEGER PRIMARY KEY,
  `id_telegram` bigint(20) NOT NULL UNIQUE,
  `saldo` float NOT NULL DEFAULT '0',
  `codigo_idioma` varchar(100) NOT NULL DEFAULT 'pt',
  `pais` int(11) NOT NULL DEFAULT '73'
);


DROP TABLE IF EXISTS `referencias`;
CREATE TABLE `referencias` (
  `id` INTEGER PRIMARY KEY,
  `id_telegram` bigint(20) NOT NULL,
  `id_indicado` bigint(20) NOT NULL,
  `data` bigint(20) NOT NULL
);

DROP TABLE IF EXISTS `alertas`;
CREATE TABLE `alertas` (
  `id` INTEGER PRIMARY KEY,
  `id_telegram` bigint(20) NOT NULL,
  `id_servico` varchar(100) NOT NULL
);