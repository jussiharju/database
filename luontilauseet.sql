-- Ryhmä 8
-- Tietokannan luontilauseet


CREATE TABLE asiakas
(atunnus INTEGER NOT NULL,
animi VARCHAR(40) NOT NULL,
aosoite VARCHAR(40) NOT NULL,
PRIMARY KEY (atunnus));

CREATE TABLE tyokohde
(tktunnus INTEGER NOT NULL,
tknimi VARCHAR(40) NOT NULL,
tkosoite VARCHAR(40) NOT NULL,
atunnus INTEGER NOT NULL,
PRIMARY KEY (tktunnus),
FOREIGN KEY (atunnus) REFERENCES asiakas(atunnus));

CREATE TABLE tyosuoritus
(tstunnus INTEGER NOT NULL,
suunnitteluh INTEGER DEFAULT 0,
aputyoh INTEGER DEFAULT 0,
tyoh INTEGER DEFAULT 0,
suunnitteluale NUMERIC(3,2) DEFAULT 1,
aputyoale NUMERIC(3,2) DEFAULT 1,
tyoale NUMERIC(3,2) DEFAULT 1,
tkttunnus INTEGER NOT NULL,
PRIMARY KEY (tstunnus),
FOREIGN KEY (tkttunnus) REFERENCES tyokohde(tktunnus),
CHECK(suunnitteluh >= 0 AND aputyoh >= 0 AND tyoh >= 0));

CREATE TABLE tarvike
(ttunnus INTEGER NOT NULL,
nimike VARCHAR(40),
yksikko CHAR(3) CHECK(yksikko = 'm' OR yksikko = 'kpl'),
myyntihinta NUMERIC(6,2) NOT NULL,
sisaanostohinta NUMERIC(6,2) NOT NULL,
alv INTEGER NOT NULL,
varastotilanne NUMERIC(6,2),
PRIMARY KEY (ttunnus));

CREATE TABLE tarvikelista
(tstunnus INTEGER NOT NULL,
ttunnus INTEGER NOT NULL,
maara NUMERIC(6,2),
alennus NUMERIC(3,2) DEFAULT 1,
PRIMARY KEY (tstunnus, ttunnus),
FOREIGN KEY (tstunnus) REFERENCES tyosuoritus(tstunnus),
FOREIGN KEY (ttunnus) REFERENCES tarvike(ttunnus));

CREATE TABLE lasku
(ltunnus INTEGER NOT NULL,
numero INTEGER DEFAULT 1 CHECK(numero > 0),
osa NUMERIC(3,2) DEFAULT 1,
alv INTEGER NOT NULL,
tila VARCHAR(20) NOT NULL,
summa NUMERIC(8,2),
erapvm DATE NOT NULL,
pvm DATE NOT NULL,
maksupvm DATE NOT NULL,
kotitalousvah NUMERIC(8,2),
tstunnus INTEGER NOT NULL,
PRIMARY KEY (ltunnus),
FOREIGN KEY (tstunnus) REFERENCES tyosuoritus(tstunnus),
CHECK (erapvm > pvm));

CREATE TABLE poistunuttarvike
(ttunnus INTEGER NOT NULL,
nimike VARCHAR(40),
pvm DATE NOT NULL,
yksikko CHAR(3) CHECK(yksikko = 'm' OR yksikko = 'kpl'),
myyntihinta NUMERIC(6,2) NOT NULL,
PRIMARY KEY (ttunnus));


