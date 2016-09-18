## SGQL Demo

#### Graph structure
```
mode: closed

`customers`: [
        `id`:      integer auto
        `name`:    string
]

`orders`: [
        `id`:      integer auto
        `cost`:    double
        `shipped`: boolean
]

`customers` <- `orders`
```

### Build the graph

#### Add customers to the graph
```
INSERT `customers`:[`name`] VALUES
        `customers`:["Larry Ellison"],
        `customers`:["Bill Gates"],
        `customers`:["Mark Zuckerberg"],
        `customers`:["Steve Jobs"]
```

#### Log customer's orders
```
INSERT `orders`:[`cost`,`shipped`] VALUES
        `orders`:[12.5, 0]
        ASSOCIATE WITH `customers`:`id` == 1
```

```
INSERT `orders`:[`cost`,`shipped`] VALUES
        `orders`:[200, 0],
        `orders`:[44.5, 0]
        ASSOCIATE WITH `customers`:`id` == 2
```

```
INSERT `orders`:[`cost`,`shipped`] VALUES
        `orders`:[9.8, 0]
        ASSOCIATE WITH `customers`:`id` == 3
```

```
INSERT `orders`:[`cost`,`shipped`] VALUES
        `orders`:[77.42, 0]
        ASSOCIATE WITH `customers`:`id` == 4
```


#### Insert a customer and their order at the same time
```
INSERT `customers`:[`name`,`orders`:[`cost`,`shipped`]] VALUES
        `customers`:["Jack Dorsey",`orders`:[53.2, 0]]
```
This inserts Jack Dorsey as a customer, and automatically creates and associates an order with him with a cost of $53.20 that hasn't shipped.  Multiple orders could have been inserted when his customer account was created by using parameters that were arrays, such as:
```
// ?cost = [59.2, 60.22, 12.9, 97]
// ?shipped = [0, 0, 0, 0]

... VALUES `customers`:["Jack Dorsey",`orders`:[?cost, ?shipped]]
```
This would have created and associated 4 orders with the new customer.  Parameters are required to add multiple nested records so that the query is more readable.

#### Update some orders to shipped

```
UPDATE `orders`
        SET `shipped` = 1
        WHERE `orders`.`customers`:(`id` == 2)
```

```
UPDATE `orders`
        SET `shipped` = 1
        WHERE `orders`.`customers`:(`name` == "Steve Jobs" AND `id` == 4)
```

#### Show all customers in alphabetical order, and their orders in decreasing order of cost
Query:
```
SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`]]
        ORDER `customers` BY `name` ASC, `customers`.`orders` BY `cost` DESC
```

Result:
```
'customers': [
        {
                'id': 2,
                'name': 'Bill Gates',
                'orders': [
                        {
                                'id': 3,
                                'cost': 200,
                                'shipped': 1
                        },
                        {
                                'id': 2,
                                'cost': 44.5,
                                'shipped': 1
                        }
                ]
        },
        {
                'id': 5,
                'name': 'Jack Dorsey',
                'orders': [
                        {
                                'id': 6,
                                'cost': 53.2,
                                'shipped': 0
                        }
                ]
        },
        {
                'id': 1,
                'name': 'Larry Ellison',
                'orders': [
                        {
                                'id': 1,
                                'cost': 12.5,
                                'shipped': 0
                        }
                ]
        },
        {
                'id': 3,
                'name': 'Mark Zuckerberg',
                'orders': [
                        {
                                'id': 4,
                                'cost': 9.8,
                                'shipped': 0
                        }
                ]
        },
        {
                'id': 4,
                'name': 'Steve Jobs',
                'orders': [
                        {
                                'id': 5,
                                'cost': 77.42,
                                'shipped': 1
                        }
                ]
        }
]
```

#### Show only the customer that has order 3
Query:
```
SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`]]
        WHERE `customers`.`orders`:(`id` == 3) AND `customers`:(COUNT(`orders`) > 0)
```

Result:
```
'customers': [
        {
                'id': 2,
                'name': 'Bill Gates',
                'orders': [
                        {
                                'id': 3,
                                'cost': 200,
                                'shipped': 1
                        }
                ]
        }
]
```


#### Show only the customer that has order 3 and show all of their orders

Query:
```
SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`]]
        WHERE `customers`.`orders`:(HAS `id` == 3) AND `customers`:(COUNT(`orders`) > 0)
```

Result:
```
'customers': [
        {
                'id': 2,
                'name': 'Bill Gates',
                'orders': [
                        {
                                'id': 2,
                                'cost': 44.5,
                                'shipped': 1
                        },
                        {
                                'id': 3,
                                'cost': 200,
                                'shipped': 1
                        }
                ]
        }
]
```
Since a customer can have multiple orders associated with them, the `HAS` keyword is used to see if one of the orders associated with the customer has the id 2.  Since this is a check and not a filter, all of the orders are shown.

#### Show only order 5 and the customer associated with it

Query:
```
SELECT `orders`:[`id`,`cost`,`shipped`,`customers`:[`id`,`name`]]
        WHERE `orders`:(`id` == 5)
```

Result:
```
`orders`: [
        {
                'id': 5,
                'cost': 77.42,
                'shipped': 1,
                'customers': [
                        {
                                'id': 4,
                                'name': 'Steve Jobs'
                        }
                ]
        }
]
```
Note that even though an order can only have one customer, the customer is still sent back in an array.  This is so that if the graph structure is ever changed to that multiple customers can be associated with an order, the code will not require any modifications to handle multiple customers being returned.
