create table reservas_quartos (
    quarto_id int not null auto_increment primary key,
    nome varchar(50) not null,
    descricao LONGTEXT,
    max_hospedes int not null default 1,
    qtde int not null default 1,
    valor_min float not null default 1,
    tamanho_m2 int,
    link varchar(60) not null default,
    publicar bool not null default 1,
    deletado bool not null default 0
) engine=innodb;


insert into dlx_menu (nome) values ('Apart Hotel');
set @menu_id = last_insert_id();

insert into dlx_menu_item (menu_id, nome, link) values
    (@menu_id, 'Quartos', '/painel-dlx/apart-hotel/quartos'),
    (@menu_id, 'Disponibilidade', '/painel-dlx/apart-hotel/disponibilidade');

set @item_quarto = (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/quartos');
set @item_dispon = (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/disponibilidade');

insert into dlx_menu_item_x_permissao
    select @item_quarto, permissao_usuario_id from dlx_permissoes_usuario where alias = 'VER_LISTA_QUARTOS'
    union
    select @item_quarto, permissao_usuario_id from dlx_permissoes_usuario where alias = 'GERENCIAR_DISPONIBILIDADE';

create table reservas_disponibilidade_valores (
    dispon_id int not null,
    qtde_pessoas int not null,
    valor decimal (10,2) not null,
    primary key (dispon_id, qtde_pessoas)
) engine=innodb;