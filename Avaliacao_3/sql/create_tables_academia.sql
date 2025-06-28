-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
id_usuario INT AUTO_INCREMENT PRIMARY KEY,
nome_completo VARCHAR(255) NOT NULL,
email VARCHAR(255) UNIQUE NOT NULL,
senha VARCHAR(255) NOT NULL,
tipo_usuario ENUM('aluno', 'professor', 'admin') DEFAULT 'aluno',
data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de modalidades
CREATE TABLE IF NOT EXISTS modalidades (
id_modalidade INT AUTO_INCREMENT PRIMARY KEY,
nome_modalidade VARCHAR(100) UNIQUE NOT NULL,
descricao TEXT NULL
);

-- Criação da tabela de professores
CREATE TABLE IF NOT EXISTS professores (
id_professor INT AUTO_INCREMENT PRIMARY KEY,
nome_professor VARCHAR(255) NOT NULL,
email VARCHAR(255) UNIQUE NULL,
telefone VARCHAR(20) NULL,
id_usuario INT NULL,
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

-- Criação da tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
id_aluno INT AUTO_INCREMENT PRIMARY KEY,
nome_aluno VARCHAR(255) NOT NULL,
email VARCHAR(255) UNIQUE NULL,
telefone VARCHAR(20) NULL,
data_nascimento DATE NULL,
id_usuario INT NULL,
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

-- Criação da tabela de aulas
CREATE TABLE IF NOT EXISTS aulas (
id_aula INT AUTO_INCREMENT PRIMARY KEY,
id_modalidade INT NOT NULL,
id_professor INT NOT NULL,
data_aula DATE NOT NULL,
hora_inicio TIME NOT NULL,
hora_fim TIME NOT NULL,
local_aula VARCHAR(255) NULL,
capacidade_maxima INT NULL,
FOREIGN KEY (id_modalidade) REFERENCES modalidades(id_modalidade) ON DELETE CASCADE,
FOREIGN KEY (id_professor) REFERENCES professores(id_professor) ON DELETE CASCADE
);

-- Criação da tabela de matrículas (relacionamento entre alunos e aulas)
CREATE TABLE IF NOT EXISTS matriculas_aula (
id_matricula INT AUTO_INCREMENT PRIMARY KEY,
id_aluno INT NOT NULL,
id_aula INT NOT NULL,
data_matricula DATETIME DEFAULT CURRENT_TIMESTAMP,
UNIQUE KEY (id_aluno, id_aula),
FOREIGN KEY (id_aluno) REFERENCES alunos(id_aluno) ON DELETE CASCADE,
FOREIGN KEY (id_aula) REFERENCES aulas(id_aula) ON DELETE CASCADE
);

-- Índices para otimizar consultas
ALTER TABLE usuarios ADD INDEX idx_email (email);
ALTER TABLE modalidades ADD INDEX idx_nome_modalidade (nome_modalidade);
ALTER TABLE professores ADD INDEX idx_email (email);
ALTER TABLE professores ADD INDEX idx_nome_professor (nome_professor);
ALTER TABLE alunos ADD INDEX idx_email (email);
ALTER TABLE alunos ADD INDEX idx_nome_aluno (nome_aluno);
ALTER TABLE aulas ADD INDEX idx_id_modalidade (id_modalidade);
ALTER TABLE aulas ADD INDEX idx_id_professor (id_professor);
ALTER TABLE aulas ADD INDEX idx_data_aula (data_aula);
ALTER TABLE matriculas_aula ADD INDEX idx_id_aluno (id_aluno);
ALTER TABLE matriculas_aula ADD INDEX idx_id_aula (id_aula);