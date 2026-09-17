CREATE DATABASE biblioteca;

USE biblioteca;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    ano INT NOT NULL
);

INSERT INTO usuarios (nome, email) VALUES
('João Silva', 'joao@email.com'),
('Maria Souza', 'maria@email.com');

INSERT INTO livros (titulo, autor, ano) VALUES
('Dom Casmurro', 'Machado de Assis', 1899),
('O Hobbit', 'J.R.R. Tolkien', 1937),
('1984', 'George Orwell', 1949);