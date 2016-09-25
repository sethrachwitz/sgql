# SGQL
The purpose of this project is to take the best parts of SQL and combine them with the principles of a graph database.

SQL is perhaps the best language for structuring database queries, but not so great at natively supporting multidimensional relationships between tables.  Graph databases are actually already here, but they are mainly in the form of huge backends built around SQL databases.

Graph query languages like GraphQL do well with multidimensional database relationships, but the language used to query and augment those relationships is not nearly as powerful as SQL, so a relatively large backend is still required.

SGQL has two main goals - be as easy to understand as SQL, and not require any code to be written to build a fully functioning graph database.  I would hope that the language specified in this project is a good start along that path.

## Terms

**Graph** - collection of schemas and their associations

**Schema** - analogous to tables in traditional SQL databases; all records inside of a schema have the same columns

**Record** - a single element within a schema, with data from multiple columns

**Association** - relationship between schemas that allows records in one schema to be associated with records in another schema

**Column** - named part of a schema that holds one piece of information for a record, and has a specific datatype

**Namespace** - used to identify a specified chain of associated schemas; namespaces also allow queries to be very readable when records of the same schema are related (`people.people` is the namespace of people associated with other people, whereas `people` is just the namespace of individual people, and `cities.zip_codes` is the namespace of zip codes associated with cities, whereas `cities` is just the namespace of cities, and has no concept of the `zip_codes` schema)

**Namespace termination** - identified by the symbol `:`; used to clearly indicate which namespace is being used, in order to allow simple column names or associated schema names to be used in situations like comparisons; instead of requiring the entire namespace to be prepended to everything, it is only prepended to the namespace terminator (i.e., `people.people:(name == ...)`; the only way to access a column or schema within a namespace is by first terminating the namespace


## Datatypes

There are 5 datatypes: `boolean`, `integer`, `double`, `string`, and `null`.  Comparisons of datatypes results in one element being "upgraded" to another datatype.  The upgrade path is boolean -> integer -> double -> string -> null (any value upgraded to null is non-null).  For instance, if an integer and a boolean were being compared, the boolean would be upgraded to an integer (1 for true, 0 for false), and then the comparison would take place.

## Graph structure file

Graph structures determine how certain schemas can be associated with each other.  A pair of schemas can only be associated once in the graph structure file.

### Modes
Two modes are available that allow for fine-grained control over which records can be associated with each other.

#### Closed mode
Closed mode means that the graph structure file explicitly details the graph structure, and any associations not detailed in the graph file are not allowed.  For instance, if the graph structure mode was `closed` and the `cities` and `zip_codes` schemas were not associated in the graph structure, associations between the two would not be allowed.

#### Open mode
Open mode still allows the graph structure to be declared, but any relationships not clearly outlined in the file would be assumed to be many-many.  For instance, if the graph structure mode was `open` and the `cities` and `zip_codes` schemas were not associated in the graph structure, it would be assumed that a city could possibly have many zip codes associated with it, and a zip code could possibly have many cities associated with it.


### Schema declarations
Schemas must be explicitly declared, in order to make it easier to visualize the graph.

In addition to the datatypes, an additional option is available when specifying a column's datatype: `integer auto`.  This will generate an auto-incremented integer for each new record inserted into the schema.  It is not required to have an `integer auto` column, but it is recommended.

`null` is not a valid datatype for a column, however it can be appended if a column is optional, meaning that a record will have a `null` value in a column if it is created without that value, or with a `null` in the column (i.e., a column with the datatype `integer null` can either store an integer, or null).  If `null` is not appended to the column datatype, `null` will not be a valid value for that column in `INSERT` or `UPDATE` operations on that schema.

An example of how the schema is declared is below.


### Types of relationships
Note that all of the relationships below are non-directional and only indicate the number of records in a schema that can be associated with records in another schema.  The graph is flat and schemas cannot be nested inside of each other.  This means that querying the namespace `customers.orders` is just as valid as querying the namespace `orders.customers`.

#### One to one (`-`)
This association allows a record from one schema to be associated to only one record from another schema, such as a person and a passport.  This is represented in the structure file as a `-`.

#### Many to one (`<-`)
This association allows a record from one schema to be associated with many records from another schema, such as a person and their purchases.  This is represented in the structure file as a `<-`, with the 'one' on the left side and the 'many' on the right side, with the arrow indicating the association of many records with one.

#### Many to many (`<->`)
This association allows many records in one schema to be associated with many records in another schema, such as friendships between people.  One person can be friends with many people, and each of those people can also be the friend of many people (i.e., `people <-> people`).  This is represented in the structure file as a `<->`, with the arrows indicating that records in both schemas can be associated with many records in the other schema.


### Example graph structure
```
mode: closed

`people`: [
        `id`:      integer auto
        `name`:    string
]

`purchases`: [
        `id`:      integer auto
        `cost`:    double
]

`vacation_destinations`: [
        `id`:      auto integer
        `city`:    string null
        `country`: string
]

`people` - `passports`
`people` <- `purchases`
`people` <-> `vacation_destinations`
```


### Naming
The naming of schemas and column names is limited to the following characters:

`a-z`, `A-Z`, `0-9`, `$`, and `_`

Backticks can be used to surround schema and column names.  They are entirely optional and are only made available for readability.


## Structure of results

Results are sent back in JSON.  Each record is an object that is an element of an array which represents the schema, even if there is only one object in the array.

## Parameters
The SGQL interpreter will have a mechanism to pass in named parameters that are either single values, or arrays of values.  In some cases, parameters would be the only way to accomplish a task in a SGQL query, such as creating multiple associated records during an `INSERT`.  In most cases, however, they are purely optional and can be used in place of any constant value.

Parameters take the form `?name`, where `name` can be made up of the following characters:

`a-z`, `A-Z`, and `0-9`
