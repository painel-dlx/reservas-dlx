start transaction;

-- Quartos -------------------------------------------------------------------------------------------------------------
create table reservas.Quarto (
    quarto_id int not null auto_increment primary key,
    nome varchar(50) not null,
    descricao LONGTEXT,
    maximo_hospedes int not null default 1,
    quantidade int not null default 1,
    valor_minimo float not null default 1,
    tamanho_m2 int,
    link varchar(60) not null,
    deletado bool not null default 0
) engine = innodb;

create table reservas.QuartoMidia (
    quarto_id int not null references reservas.Quarto (quarto_id) on delete cascade,
    arquivo_original varchar(255) not null,
    miniatura varchar(255)
) engine = innodb;

-- Disponibilidade -----------------------------------------------------------------------------------------------------
create table reservas.Disponibilidade (
    disponibilidade_id int not null primary key auto_increment,
    data date not null,
    quarto_id int not null references reservas.Quarto (quarto_id) on delete cascade,
    quantidade int not null default 1 check ( quantidade >= 0 ),
    desconto decimal(13,4) not null default 0
) engine = innodb;

create table DisponibilidadeValor (
    disponibilidade_id int not null references reservas.Disponibilidade (disponibilidade_id),
    quantidade_pessoas int not null,
    valor decimal (10,2) not null,
    primary key (disponibilidade_id, quantidade_pessoas)
) engine = innodb;

-- Reservas ------------------------------------------------------------------------------------------------------------
create table reservas.Reserva (
    reserva_id int not null primary key auto_increment,
    quarto_id int not null references reservas.Quarto (quarto_id),
    hospede varchar(200) not null,
    cpf varchar(14) not null,
    telefone varchar (18),
    email varchar(200) not null,
    checkin date not null,
    checkout date not null,
    quantidade_adultos int not null default 2 check ( quantidade_adultos >= 1 ),
    quantidade_criancas int not null default 0 check ( quantidade_criancas >= 0 ),
    valor decimal(13,4) not null,
    status varchar(10) not null default 'Pendente',
    origem varchar(20) not null default 'Website'
) engine = innodb;

create table reservas.ReservaHistorico (
    reserva_historico_id  int not null auto_increment primary key,
    reserva_id int not null references reservas.Reserva(reserva_id) on delete cascade,
    usuario_id int not null references dlx.Usuario(usuario_id),
    data datetime not null,
    status varchar(10) not null,
    motivo text not null
) engine = innodb;

create table reservas.ReservaVisualizacaoCpf (
    reserva_id int not null references reservas.Reserva (reserva_id) on delete cascade,
    usuario_id int not null references dlx.Usuario (usuario_id),
    data date not null
) engine = innodb;

-- Pedidos -------------------------------------------------------------------------------------------------------------
create table reservas.Pedido (
    pedido_id int not null auto_increment primary key,
    nome varchar(100) not null,
    cpf varchar(14) not null,
    email varchar(200) not null,
    telefone varchar(16),
    valor_total decimal(13,4) not null,
    forma_pagamento varchar(10) not null default 'digitada',
    status varchar(10) not null default 'Pendente'
) engine = innodb;

-- Itens de pedidos ----------------------------------------------------------------------------------------------------
drop table if exists reservas.PedidoItem;
create table reservas.PedidoItem (
    pedido_item_id int not null auto_increment primary key,
    pedido_id int not null references reservas.Pedido (pedido_id),
    quarto_id int not null references reservas.Quarto (quarto_id),
    checkin datetime not null,
    checkout datetime  not null,
    quantidade int not null check (quantidade > 0),
    quantidade_adultos int not null check (quantidade_adultos > 0),
    quantidade_criancas int not null,
    valor_total decimal(10,4),
    reserva_id int references reservas.Reserva(reserva_id)
) engine = innodb;

create table reservas.PedidoItemDetalhe (
    pedido_item_detalhe_id int not null primary key auto_increment,
    pedido_item_id int not null references reservas.PedidoItem (pedido_item_id) on delete cascade,
    data date not null,
    diaria decimal(13,4) not null,
    desconto decimal(13,4) not null default 0 check ( desconto between 0.00 and 99.99 )
) engine = innodb;

-- Dados do Cartão de Crédito ------------------------------------------------------------------------------------------
drop table if exists reservas.PedidoCartao;
create table reservas.PedidoCartao (
    pedido_id int not null references reservas.Pedido (pedido_id),
    tipo varchar(10) not null default 'credit' check ( tipo in ('debit', 'credit') ),
    dono varchar(100) not null,
    numero_cartao varchar(20) not null,
    validade varchar(7) not null,
    codigo_seguranca varchar(5),
    valor float not null,
    parcelas int not null default 1 check (parcelas > 0)
) engine = innodb;

drop table if exists reservas.PedidoEndereco;
create table reservas.PedidoEndereco (
    pedido_id int not null references reservas.Pedido (pedido_id),
    cep varchar(9) not null,
    logradouro varchar(200) not null,
    numero varchar(10),
    bairro varchar(50) not null,
    cidade varchar(50) not null,
    uf char(2) not null,
    complemento varchar(200)
) engine = innodb;

drop table if exists reservas.PedidoHistorico;
create table reservas.PedidoHistorico (
    pedido_historico_id int not null auto_increment primary key,
    pedido_id int not null references reservas.Pedido (pedido_id),
    usuario_id int not null references dlx.Usuario (usuario_id),
    data datetime not null,
    status varchar(10) not null,
    motivo text not null
) engine = innodb;

-- INSERIR INFORMAÇÕES -------------------------------------------------------------------------------------------------
insert into dlx.PermissaoUsuario (alias, descricao) values
    ('VER_LISTA_QUARTOS', 'Ver a lista de quartos.'),
    ('GERENCIAR_DISPONIBILIDADE', 'Gerenciar disponibilidade de quartos para reserva.'),
    ('VER_LISTA_RESERVAS', 'Ver a lista de reservas cadastradas.'),
    ('VER_LISTA_PEDIDOS', 'Ver a lista de pedidos enviados pelo site.');

insert into dlx.Menu (nome) values ('Apart Hotel');
set @menu_id = last_insert_id();

insert into dlx.MenuItem (menu_id, nome, link) values
    (@menu_id, 'Quartos', '/painel-dlx/apart-hotel/quartos'),
    (@menu_id, 'Disponibilidade', '/painel-dlx/apart-hotel/disponibilidade'),
    (@menu_id, 'Reservas', '/painel-dlx/apart-hotel/reservas'),
    (@menu_id, 'Pedidos', '/painel-dlx/apart-hotel/pedidos-pendentes');

set @item_quarto = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/quartos');
set @item_dispon = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/disponibilidade');
set @item_reserva = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/reservas');
set @item_pedidos = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/pedidos-pendentes');

insert into dlx.MenuItem_x_PermissaoUsuario
select @item_quarto, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_QUARTOS'
union
select @item_dispon, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'GERENCIAR_DISPONIBILIDADE'
union
select @item_reserva, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_RESERVAS'
union
select @item_pedidos, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_PEDIDOS';

insert into dlx.PermissaoUsuario_x_GrupoUsuario (grupo_usuario_id, permissao_usuario_id)
    select
        g.grupo_usuario_id,
        p.permissao_usuario_id
    from
        dlx.PermissaoUsuario p,
        dlx.GrupoUsuario g
    where
        g.alias = 'ADMIN'
        and p.alias in('VER_LISTA_QUARTOS', 'GERENCIAR_DISPONIBILIDADE', 'VER_LISTA_RESERVAS', 'VER_LISTA_PEDIDOS');

-- Inserir o novo Widget
insert into dlx.Widget (titulo, url_conteudo) value ('Novos Pedidos', '/painel-dlx/apart-hotel/pedidos/quantidade-pedidos-pendentes');
# insert into reservas_pedido_cartao_credito (pedido_id, dono, numero_cartao, validade, codigo_seguranca, valor, parcelas)
#    select pgto_pedido, pgto_cartao_dono, pgto_cartao_numero, pgto_cartao_expiracao, pgto_cartao_codseg, pgto_valor, pgto_parcelas from dlx_reservas_pgto_cartao;

-- Excluir o campo pedido_itens da tabela dlx_reservas_pedidos pois não será mais utilizados
-- OBS: Executar esse drop apenas depois de ajustar os registros do PedidoItem
# alter table dlx_reservas_pedidos drop pedido_itens;

rollback;
commit;