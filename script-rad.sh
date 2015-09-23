if [ -z "$1" ]
  then
    echo "Nome do projeto não informado"
    exit
fi

git clone https://github.com/cirovargas/SymfonyRadBundleSet.git $1
cd $1
curl -sS https://getcomposer.org/installer | php
php composer.phar install
php app/console doctrine:mapping:import AppBundle annotation
sleep 5
#for f in  src/Project/AdminBundle/Entity/* ; do folder=(${f//\// }) ; entity=${folder[${#folder[@]}-1]//\.php/ }; php app/console doctrine:generate:entities ProjectAdminBundle:${entity} ; done
php app/console doctrine:generate:entities AppBundle
sleep 5

rm src/AppBundle/Entity/*.php~
php app/console doctrine:schema:update --force --complete
php app/console fos:user:create

for f in  src/Project/AdminBundle/Entity/* ; do folder=(${f//\// }) ; entity=${folder[${#folder[@]}-1]//\.php/ }; php app/console jordillonch:generate:crud --entity=AppBundle:${entity} --route-prefix=/${entity} --with-write --format=yml --overwrite --no-interaction   ; done
php app/console cache:clear

php app/console assets:install --symlink
php app/console assetic:dump
chmod -R 777 *

