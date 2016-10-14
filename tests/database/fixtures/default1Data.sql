USE sgql_unittests_data_1;

INSERT INTO `customers` (`name`,`vip`) VALUES
    ('Steve Jobs', 0),
    ('Larry Elison', 1),
    ('Mark Zuckerburg', 0),
    ('Jack Dorsey', 1)
;

INSERT INTO `orders` (`cost`,`shipped`,`customer`) VALUES
    (22.5, 0, 1),
    (19.1, 1, 2),
    (55.2, 0, 3)
;
