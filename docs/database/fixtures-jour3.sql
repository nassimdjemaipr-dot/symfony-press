-- SymfonyPress - Dataset pédagogique JOUR 3
-- À importer après les migrations du Jour 3
-- Ce fichier contient des utilisateurs et des articles avec user_id

-- Catégories
INSERT INTO category (name, slug) VALUES
('Symfony', 'symfony'),
('PHP', 'php'),
('DevOps', 'devops'),
('Frontend', 'frontend'),
('Architecture', 'architecture');

-- Utilisateurs (mot de passe: "password" hashé avec bcrypt)
-- Le hash correspond à "password"
INSERT INTO user (email, roles, password) VALUES
('admin@example.com', '["ROLE_ADMIN"]', '$2y$13$hK1kVxLZ3QVqVPvvxJQZaO9BhT7PQF.VVIiGKJSEhYGgKPqGxgEiG'),
('user@example.com', '[]', '$2y$13$hK1kVxLZ3QVqVPvvxJQZaO9BhT7PQF.VVIiGKJSEhYGgKPqGxgEiG');

-- Articles avec user_id (author_id)
INSERT INTO article (title, slug, content, created_at, category_id, author_id, image) VALUES
('Découvrir Symfony 8', 'decouvrir-symfony-8',
'Symfony 8 apporte de nombreuses améliorations en performance et en DX. Le nouveau composant AssetMapper simplifie la gestion des assets frontend sans build step. Les attributs PHP 8 permettent une configuration plus claire et concise.', NOW(), 1, 1, NULL),

('Twig pour les débutants', 'twig-pour-les-debutants',
'Twig est le moteur de template par défaut de Symfony. Sa syntaxe claire et sécurisée permet de créer des vues maintenables. Les filtres, fonctions et extensions offrent une grande flexibilité pour manipuler les données côté template.', NOW(), 1, 1, NULL),

('Les bases solides de PHP moderne', 'php-moderne',
'PHP 8 apporte le typage strict, les attributes, les enums et les propriétés promues. Ces fonctionnalités permettent un code plus robuste et expressif. L''écosystème PHP moderne inclut Composer, PHPStan et des outils de qualité professionnels.', NOW(), 2, 1, NULL),

('Doctrine et les relations', 'doctrine-relations',
'Les jointures sont au cœur de toute application métier. Doctrine ORM permet de mapper les relations de base de données en objets PHP. ManyToOne, OneToMany, ManyToMany : chaque type de relation a ses spécificités et ses bonnes pratiques.', NOW(), 5, 2, NULL),

('Organiser un projet Symfony', 'architecture-symfony',
'Une bonne architecture permet un projet maintenable sur le long terme. Symfony impose une structure MVC claire : contrôleurs, entités, repositories, services. Les bundles permettent de modulariser les fonctionnalités.', NOW(), 5, 2, NULL),

('Symfony vs Laravel', 'symfony-vs-laravel',
'Comparaison entre deux frameworks majeurs de l''écosystème PHP. Symfony privilégie la robustesse et la flexibilité, Laravel met l''accent sur la rapidité de développement. Les deux approches ont leurs avantages selon le contexte projet.', NOW(), 1, 1, NULL),

('Bases du frontend moderne', 'frontend-moderne',
'HTML, CSS et JS sont les fondations du web. Les standards modernes incluent les Web Components, CSS Grid, et l''API Fetch. Stimulus et Turbo permettent d''ajouter de l''interactivité sans framework lourd.', NOW(), 4, 2, NULL),

('Déployer une app Symfony', 'deployer-symfony',
'Docker, Nginx et CI/CD sont les piliers du déploiement moderne. Symfony s''adapte à tous les environnements : serveur dédié, VPS, cloud, PaaS. Les bonnes pratiques incluent les variables d''environnement et la gestion des secrets.', NOW(), 3, 1, NULL);