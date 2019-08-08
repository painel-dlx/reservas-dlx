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
    (@menu_id, 'Disponibilidade', '/painel-dlx/apart-hotel/disponibilidade'),
    (@menu_id, 'Reservas', '/painel-dlx/apart-hotel/reservas'),
    (@menu_id, 'Pedidos', '/painel-dlx/apart-hotel/pedidos');

set @item_quarto = (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/quartos');
set @item_dispon = (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/disponibilidade');
set @item_reserva = (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/reservas');
set @item_pedidos= (select menu_item_id from dlx_menu_item where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/pedidos');

insert into dlx_menu_item_x_permissao
    select @item_quarto, permissao_usuario_id from dlx_permissoes_usuario where alias = 'VER_LISTA_QUARTOS'
    union
    select @item_dispon, permissao_usuario_id from dlx_permissoes_usuario where alias = 'GERENCIAR_DISPONIBILIDADE'
    union
    select @item_reserva, permissao_usuario_id from dlx_permissoes_usuario where alias = 'VER_LISTA_RESERVAS'
    union
    select @item_pedidos, permissao_usuario_id from dlx_permissoes_usuario where alias = 'VER_LISTA_PEDIDOS';

create table reservas_disponibilidade_valores (
    dispon_id int not null,
    qtde_pessoas int not null,
    valor decimal (10,2) not null,
    primary key (dispon_id, qtde_pessoas)
) engine=innodb;

drop table if exists reserva_historico;
create table reserva_historico (
    reserva_historico_id  int not null auto_increment primary key,
    reserva_id int not null,
    usuario_id int not null,
    data datetime not null,
    status varchar(10) not null,
    motivo text not null,
    constraint FK_reserva_historico_usuario_id foreign key (usuario_id) references dlx_usuarios (usuario_id),
    constraint FK_reserva_historico_reserva_id foreign key (reserva_id) references dlx_reservas_cadastro (reserva_id) on delete cascade
) engine=innodb;

drop table if exists dlx_pedido_historico;
create table dlx_pedido_historico (
    pedido_historico_id int not null auto_increment primary key,
    pedido_id int not null,
    usuario_id int not null,
    data datetime not null,
    status varchar(10) not null,
    motivo text not null,
    constraint FK_pedido_historico_pedido_id foreign key (pedido_id) references dlx_reservas_pedidos (pedido_id) on delete cascade,
    constraint FK_pedido_historico_usuario_id foreign key (usuario_id) references dlx_usuarios (usuario_id)
) engine=innodb;

-- Itens de pedidos ----------------------------------------------------------------------------------------------------
drop table if exists reservas_pedido_itens;
create table reservas_pedido_itens (
    pedido_item_id int not null auto_increment primary key,
    pedido_id int not null references dlx_reservas_pedidos (pedido_id),
    quarto_id int not null references dlx_reservas_quartos (quarto_id),
    checkin datetime not null,
    checkout datetime  not null,
    quantidade int not null check (quantidade > 0),
    adultos int not null check (adultos > 0),
    criancas int not null,
    valor_total decimal(10,4)
) engine=innodb;


-- Dados do Cartão de Crédito ------------------------------------------------------------------------------------------
drop table if exists reservas_pedido_cartao_credito;
create table reservas_pedido_cartao_credito (
    pedido_id int not null references dlx_reservas_pedidos (pedido_id),
    dono varchar(100) not null,
    numero_cartao varchar(20) not null,
    validade varchar(7) not null,
    codigo_seguranca varchar(5),
    valor float not null,
    parcelas int not null default 1 check (parcelas > 0)
) engine=innodb;

drop table if exists reservas_pedidos_enderecos;
create table reservas_pedidos_enderecos (
    pedido_id int not null references dlx_reservas_pedidos (pedido_id),
    cep varchar(8) not null,
    logradouro varchar(200) not null,
    numero varchar(10),
    bairro varchar(50) not null,
    cidade varchar(50) not null,
    uf char(2) not null,
    complemento varchar(200)
) engine=innodb;

insert into reservas_pedido_cartao_credito (pedido_id, dono, numero_cartao, validade, codigo_seguranca, valor, parcelas)
    select pgto_pedido, pgto_cartao_dono, pgto_cartao_numero, pgto_cartao_expiracao, pgto_cartao_codseg, pgto_valor, pgto_parcelas from dlx_reservas_pgto_cartao