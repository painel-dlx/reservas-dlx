start transaction;

-- Disponibilidade -----------------------------------------------------------------------------------------------------
alter table reservas.dlx_reservas_disponibilidade add column desconto decimal (13,4) not null default 0;
alter table reservas.dlx_reservas_disponibilidade rename to reservas.Disponibilidade;
alter table reservas.Disponibilidade change dispon_id disponibilidade_id int not null;
alter table reservas.Disponibilidade change dispon_dia data date not null;
alter table reservas.Disponibilidade change dispon_quarto quarto_id int not null;
alter table reservas.Disponibilidade change dispon_qtde quantidade int not null default 1;

alter table reservas.reservas_disponibilidade_valores rename to reservas.DisponibilidadeValor;
alter table reservas.DisponibilidadeValor change dispon_id disponibilidade_id int not null;
alter table reservas.DisponibilidadeValor change qtde_pessoas quantidade_pessoas int not null;

-- Quarto --------------------------------------------------------------------------------------------------------------
alter table reservas.dlx_reservas_quartos rename to reservas.Quarto;
alter table reservas.Quarto change quarto_nome nome varchar(50) not null;
alter table reservas.Quarto change quarto_descricao descricao longtext;
alter table reservas.Quarto change quarto_maxhospedes maximo_hospedes int not null default 1 check (maximo_hospedes > 0);
alter table reservas.Quarto change quarto_qtde quantidade int not null default 0 check (quantidade >= 0);
alter table reservas.Quarto change quarto_valor_min valor_minimo decimal (10,2) not null default 0.00 check (valor_minimo > 0);
alter table reservas.Quarto change quarto_tamanho_m2 tamanho_m2 decimal (10,2) not null default 0.00 check (tamanho_m2 > 0);
alter table reservas.Quarto change quarto_link link varchar(60) not null;
alter table reservas.Quarto drop quarto_publicar;
alter table reservas.Quarto change quarto_delete deletado bool not null default 0;

alter table reservas.dlx_reservas_quartos_midias rename to reservas.QuartoMidia;
alter table reservas.QuartoMidia change midia_quarto quarto_id int not null;
alter table reservas.QuartoMidia change midia_arquivo arquivo_original varchar(255) not null;
alter table reservas.QuartoMidia change midia_mini miniatura varchar(255);

-- Reservas ------------------------------------------------------------------------------------------------------------
alter table reservas.dlx_reservas_cadastro rename to reservas.Reserva;
alter table reservas.Reserva change reserva_pedido pedido_id int;
alter table reservas.Reserva change reserva_quarto quarto_id int not null;
alter table reservas.Reserva change reserva_hospede hospede varchar(200) not null;
alter table reservas.Reserva change reserva_cpf cpf varchar(14) not null;
alter table reservas.Reserva change reserva_telefone telefone varchar(18);
alter table reservas.Reserva change reserva_email email varchar(200) not null;
alter table reservas.Reserva change reserva_checkin checkin date not null;
alter table reservas.Reserva change reserva_checkout checkout date not null;
alter table reservas.Reserva change reserva_adultos quantidade_adultos int not null check (quantidade_adultos > 0);
alter table reservas.Reserva change reserva_criancas quantidade_criancas int not null check (quantidade_criancas >= 0);
alter table reservas.Reserva change reserva_valor valor decimal (10,2) not null;
alter table reservas.Reserva change reserva_status status varchar(10) not null default 'Pendente';
alter table reservas.Reserva change reserva_origem origem varchar(20) not null default 'Website';

alter table reservas.dlx_reserva_visualizacoes_cpf rename to reservas.ReservaVisualizacaoCpf;

alter table reservas.reserva_historico rename to reservas.ReservaHistorico;

-- Pedidos -------------------------------------------------------------------------------------------------------------
alter table reservas.dlx_reservas_pedidos rename to reservas.Pedido;
alter table reservas.Pedido change pedido_nome nome varchar(100) not null;
alter table reservas.Pedido change pedido_cpf cpf varchar(14) not null;
alter table reservas.Pedido change pedido_email email varchar(200) not null;
alter table reservas.Pedido change pedido_telefone telefone varchar(16);
alter table reservas.Pedido change pedido_valor_total valor_total decimal(13,4) not null;
alter table reservas.Pedido change pedido_pgto_via forma_pagamento varchar(10) not null default 'digitada';
alter table reservas.Pedido change pedido_status status varchar(10) not null default 'Pendente';
alter table reservas.Pedido drop pedido_pgto_tid;
alter table reservas.Pedido drop pedido_pgto_valor;
alter table reservas.Pedido drop pedido_pgto_retorno;

alter table reservas.reservas_pedido_itens rename to reservas.PedidoItem;
alter table reservas.PedidoItem change adultos quantidade_adultos int not null check ( quantidade_adultos > 0 );
alter table reservas.PedidoItem change criancas quantidade_criancas int not null check ( quantidade_criancas >= 0 );

alter table reservas.dlx_pedido_historico rename to reservas.PedidoHistorico;

alter table reservas.reservas_pedido_cartao rename to reservas.PedidoCartao;

alter table reservas.reservas_pedidos_enderecos rename to reservas.PedidoEndereco;

create table PedidoItemDetalhe (
    pedido_item_id int not null references reservas.PedidoItem (pedido_item_id) on delete cascade,
    data date not null,
    diaria decimal(13,4) not null,
    desconto decimal(13,4) not null default 0
) engine = innodb;

-- Alterar chaves estrangeiras do usuário
alter table reservas.PedidoHistorico drop foreign key FK_pedido_historico_usuario_id;
alter table reservas.PedidoHistorico add constraint foreign key (usuario_id) references dlx.Usuario (usuario_id);

alter table reservas.ReservaHistorico drop foreign key FK_reserva_historico_usuario_id;
alter table reservas.ReservaHistorico add constraint foreign key (usuario_id) references dlx.Usuario (usuario_id);

commit;