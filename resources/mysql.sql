-- #! mysql
-- #{ refermebro

-- # { init
CREATE TABLE IF NOT EXISTS refermebro
(
    uuid VARCHAR(32) PRIMARY KEY,
    name VARCHAR(32),
    referral VARCHAR (32),
    cmds VARCHAR,
    referred INT DEFAULT 0,
    refers INT DEFAULT 0
    );
-- # }

-- # { load
-- #   :uuid string
SELECT *
FROM refermebro
WHERE uuid=:uuid;
-- # }

-- # { loadfromcode
-- #   :referral string
SELECT * FROM refermebro
WHERE referral=:referral;
-- # }

-- # { create
-- #   :uuid string
-- #   :name string
-- #   :referral string
-- #   :cmds string
INSERT INTO refermebro (uuid, name, referral, cmds)
VALUES (:uuid, :name, :referral, :cmds);
-- # }

-- # { update
-- #    :uuid string
-- #    :name string
-- #    :cmds string
-- #    :referred int
-- #    :refers int
UPDATE refermebro
SET refers=:refers,
    name=:name,
    cmds=:cmds,
    referred=:referred
WHERE uuid=:uuid;
-- # }

-- # }