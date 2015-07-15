DROP TABLE IF EXISTS Factos ;
CREATE TABLE Factos( 
	localID 		INT NOT NULL,    					
	tempoID  		Date NOT NULL,			
	valor 			INT,
	leilao			INT,
	PRIMARY KEY (localID,tempoID,leilao),
	FOREIGN KEY (localID) references LocalDim(localID),
	FOREIGN KEY (tempoID) references Tempo(tempoID)
);

DROP TABLE IF EXISTS LocalDim ;
CREATE TABLE LocalDim( 
	localID 		INT AUTO_INCREMENT,    					
	Regiao  		VARCHAR(80),			
	Concelho     	VARCHAR(80),
	PRIMARY KEY (localID)
);

DROP TABLE IF EXISTS Tempo ;
CREATE TABLE Tempo( 
	tempoID 		Date NOT NULL,					
	Dia  			INT,			
	Mes     		INT,
	Ano 			INT,
	PRIMARY KEY (tempoID)
);


#popula Tempo
INSERT INTO Tempo (tempoID,Ano,Mes,Dia) select distinct dia,YEAR(dia),MONTH(dia),DAY(dia) from leilao;

#popula Local
INSERT INTO LocalDim (Regiao,Concelho) select distinct regiao, concelho from leiloeira;

#popula tabela Factos
insert into Factos(tempoID, localID, valor,leilao)
select dia, LocalDim.localID, max(valor),leilao
	from lance natural join leilaor natural join leiloeira, LocalDim
	where lance.leilao=leilaor.lid and LocalDim.Concelho=leiloeira.concelho and LocalDim.Regiao=leiloeira.regiao
	group by lid;


#Considerando o esquema da estrela, escreva a interrogação em MySQL para obter a receita dos 
# leilões em cada concelho, com rollup por ano e mês do biénio 2012-2013.

select Concelho,sum(Valor)as quantity,Ano,Mes
from Factos f natural join LocalDim natural join Tempo
where Ano = 2012 or Ano=2013
group by Concelho,Ano,Mes with rollup;



