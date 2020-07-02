# Symfony Newspaper
My assignment project SI classes.

## installation

```bash
git clone https://github.com/KrzysztofWelc/symfony_newspaper.git

cd symfony_newspaper

composer install
```
create ```.env``` file based on ```.env.placeholder``` file.

create directory ```uploads/avatars``` in ```public``` directory.

```bash
chmod 777 ./uploads -R 
```

go to project's root directory
```
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load
symfony server:start
```