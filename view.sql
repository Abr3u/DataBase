CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `ist170542`@`%` 
    SQL SECURITY DEFINER
VIEW `MyView` AS
    select 
        `l`.`nif` AS `nif`,
        `l`.`dia` AS `dia`,
        `l`.`nrleilaonodia` AS `nrleilaonodia`,
        `lr`.`lid` AS `lid`,
        `lr`.`nrdias` AS `nrdias`,
        `l`.`valorbase` AS `valorbase`,
        (select 
                max(`laa`.`valor`)
            from
                `lance` `laa`
            where
                (`laa`.`leilao` = `lr`.`lid`)) AS `Maximo`,
        (select 
                (max(`laa`.`valor`) / `l`.`valorbase`)
            from
                `lance` `laa`
            where
                (`laa`.`leilao` = `lr`.`lid`)) AS `Racio`,
        (select (to_days((`l`.`dia` + interval `lr`.`nrdias` day)) - to_days(cast(now() as date)))) AS `diasAteAoFinal`
    from
        (`leilao` `l`
        join `leilaor` `lr` ON (((`l`.`dia` = `lr`.`dia`)
            and (`l`.`nif` = `lr`.`nif`)
            and (`l`.`nrleilaonodia` = `lr`.`nrleilaonodia`))))