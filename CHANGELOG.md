2.0.24
    
    - Rename Object to BaseObject (as Object is reserved keyword from PHP 7.2)

2.0.23
    
    - Made all string equality check strict 
    - Remove coveralls.io from dev dependencies to travis

2.0.22
    
    - Add better random id function
    - Add coveralls.io and test on PHP 5.6

2.0.21

    - Fix issue with null in Acl isAllowed

2.0.20

    - Add priority for rule.

2.0.19

    - Add license.

2.0.18

    - Update README.md

2.0.17

    - Minor refactoring

2.0.16

    - Fix problem with resetting aggregates

2.0.15

    - Add ability get aggregate from rules and RuleResult

2.0.14-2.0.12

    - Start using recursive iterators for walking by objects

2.0.11-2.0.1

    - Last added Role (Resource) to RoleAggregate (or ResourceAggregate) wins

2.0.0

    - Last added rule now win
    - Child resources NOT grant access to their parents
    - Child resource inherit access from parents
    - Add priorities

1.0.0-1.0.2

    - First version