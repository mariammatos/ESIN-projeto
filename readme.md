# Triptales
## Maria Matos - 202208005
## Marta Filipe - 202502464
## sara Gouveia - 202206979


O projeto desenvolvido consiste numa rede social de partilha de conteúdo sobre viagens, incluindo fotografias, opiniões, atividades e alojamentos. Este permite que os utilizadores se sigam mutuamente e interajam com o conteúdo publicado uns pelos outros.

Para poder aceder ao website através do Docker, basta fazer:

docker run -d -p 9000:8080 -it --name=TripTales -v "path:/var/www/html" gfcg/vesica-php73:dev

No lugar de "path", deve estar o caminho total para a pasta onde se encontra o projeto. No browser, http://localhost:9000/ dá direcionamento direto para o site.

A base de dados foi criada com base no script sql - database/tabelas.sql - e foi sendo gradualmente populada manualmente. A tabela País foi também populada, através do terminal, com todos os países do mundo atualmente reconhecidos pela Organização das Nações Unidas, de modo a evitar conflitos na inserção manual dos mesmos.

Para testar a funcionalidade do website, é possível realizar o login usando uma das combinações de nome de utilizador e palavra passe abaixo, sendo que estes utilizadores já possuem viagens publicadas e algumas interações com outros utilizadores. Também é possível criar uma nova conta, usando a opção registar.

utilizador: mariasouza   palavra-passe: pass123
utilizador: carlos_madrid   palavra-passe: pass123
utilizador: joaosilva   palavra-passe: pass123