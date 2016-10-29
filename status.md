## Status of the project

### Parser
#### Clauses
- [x] SELECT Clause
 - [x] Location graph
 - [x] Optional `WHERE` clause
 - [x] Optional `ORDER` clause
 - [x] Optional `SHOW` clause
- [x] INSERT Clause
 - [x] Location graph
 - [x] Mandatory `VALUES` clause
 - [x] Optional `ASSOCIATE` clause
- [x] UPDATE Clause
 - [x] Entity name (schema name)
 - [x] Mandatory `WHERE` clause
 - [x] Optional `SET` clause
 - [x] Optional `ASSOCIATE` clause
 - [x] Optional `DISASSOCIATE` clause
 - [x] Require at least one of the optional clauses
- [x] DELETE Clause
 - [x] Entity name (schema name)
 - [x] Optional `WHERE` clause
- [x] DESCRIBE Clause
 - [x] Entity name (schema name)
- [x] ASSOCIATE / DISASSOCIATE Clauses
- [x] ORDER Clause
- [x] SET Clause
- [x] SHOW Clause
 - [x] Paging allowed for top level schema
 - [ ] Paging allowed for all schemas (future)
- [x] VALUES Clause
- [x] WHERE Clause
 - [x] Namespaced conditions
 - [x] 'AND' keyword
 - [ ] 'OR' keyword and parenthesis, allowing complex logic (future)

#### Tokens
- [x] Location Graph
 - [x] Nested location graph
 - [x] Entity name (column name)
  - [x] Optional alias
 - [x] Aggregation function
 - [x] Count function
 - [ ] HAS function
- [x] Compare
- [x] Value Graph
- [x] SHOW schema (top level schema)
 - [x] Allow paging
- [x] SHOW namespace (nested schemas)
 - [ ] Allow paging (future)

#### Entities
- [x] Location
- [x] Column
- [x] Namespace

#### Primitives
- [x] Entity name
- [x] Value
 - [x] Parameter
 - [x] Constant
  - [x] Double
  - [x] Integer
  - [x] String
  - [x] Boolean
