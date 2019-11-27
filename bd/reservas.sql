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


insert into dlx.Menu (nome) values ('Apart Hotel');
set @menu_id = last_insert_id();

insert into dlx.MenuItem (menu_id, nome, link) values
    (@menu_id, 'Quartos', '/painel-dlx/apart-hotel/quartos'),
    (@menu_id, 'Disponibilidade', '/painel-dlx/apart-hotel/disponibilidade'),
    (@menu_id, 'Reservas', '/painel-dlx/apart-hotel/reservas'),
    (@menu_id, 'Pedidos', '/painel-dlx/apart-hotel/pedidos');

set @item_quarto = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/quartos');
set @item_dispon = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/disponibilidade');
set @item_reserva = (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/reservas');
set @item_pedidos= (select menu_item_id from dlx.MenuItem where menu_id = @menu_id and link = '/painel-dlx/apart-hotel/pedidos');

insert into dlx.MenuItem_x_PermissaoUsuario
    select @item_quarto, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_QUARTOS'
    union
    select @item_dispon, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'GERENCIAR_DISPONIBILIDADE'
    union
    select @item_reserva, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_RESERVAS'
    union
    select @item_pedidos, permissao_usuario_id from dlx.PermissaoUsuario where alias = 'VER_LISTA_PEDIDOS';

create table DisponibilidadeValor (
    disponibilidade_id int not null references reservas.Disponibilidade (disponibilidade_id),
    quantidade_pessoas int not null,
    valor decimal (10,2) not null,
    primary key (disponibilidade_id, quantidade_pessoas)
) engine = innodb;

drop table if exists reservas.ReservaHistorico;
create table reservas.ReservaHistorico (
    reserva_historico_id  int not null auto_increment primary key,
    reserva_id int not null references reservas.Reserva(reserva_id) on delete cascade,
    usuario_id int not null references dlx.Usuario(usuario_id),
    data datetime not null,
    status varchar(10) not null,
    motivo text not null
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

-- Itens de pedidos ----------------------------------------------------------------------------------------------------
drop table if exists reservas.PedidoItem;
create table reservas.PedidoItem (
    pedido_item_id int not null auto_increment primary key,
    pedido_id int not null references reservas.Pedido (pedido_id),
    quarto_id int not null references reservas.Quarto (quarto_id),
    checkin datetime not null,
    checkout datetime  not null,
    quantidade int not null check (quantidade > 0),
    adultos int not null check (adultos > 0),
    criancas int not null,
    valor_total decimal(10,4)
) engine = innodb;


-- Dados do Cartão de Crédito ------------------------------------------------------------------------------------------
drop table if exists reservas.PedidoCartao;
create table reservas.PedidoCartao (
    pedido_id int not null references reservas.Pedido (pedido_id),
    dono varchar(100) not null,
    numero_cartao varchar(20) not null,
    validade varchar(7) not null,
    codigo_seguranca varchar(5),
    valor float not null,
    parcelas int not null default 1 check (parcelas > 0)
) engine=innodb;

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

insert into reservas_pedido_cartao_credito (pedido_id, dono, numero_cartao, validade, codigo_seguranca, valor, parcelas)
    select pgto_pedido, pgto_cartao_dono, pgto_cartao_numero, pgto_cartao_expiracao, pgto_cartao_codseg, pgto_valor, pgto_parcelas from dlx_reservas_pgto_cartao;

-- Excluir o campo pedido_itens da tabela dlx_reservas_pedidos pois não será mais utilizados
-- OBS: Executar esse drop apenas depois de ajustar os registros do PedidoItem
alter table dlx_reservas_pedidos drop pedido_itens;