drop procedure if exists gerar_calendario;
create procedure gerar_calendario (data_inicial date, data_final date, quarto int)
    begin
        drop temporary table if exists calendario;
        create temporary table calendario (data date);

        insert into calendario
            select * from
                (select adddate('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) data
                 from
                     (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                     (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                     (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                     (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                     (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
            Where
                data between data_inicial and data_final;

        start transaction;
            insert into disponibilidade (data, quarto_id, quantidade)
            select
                c.data as dia,
                q.quarto_id as quarto,
                0 as quantidade
            from
                calendario c,
                dlx_reservas_quartos q
            left join
                disponibilidade drd on q.quarto_id = drd.quarto_id
            where
                drd.data is null
                and (
                    quarto is null
                    or q.quarto_id = quarto
                );
        commit;
    end;