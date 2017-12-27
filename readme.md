SQLHelper
---

Hey welcome to SQLHelper, a PHP SQL class made for people who don't have access to the MySQLi function get_result, but have access to MySQLi bind_result only. Everyone is free to use this. Just please give credit where credit is due and share the resource so others can have the pleasure of using it! Thanks for checking it out!

### How does it work?

Great question dude! The way it works is quite simple. Here are some examples and explanations :)

#### _Declaring the helper_

```
$helper = new SQLHelper('localhost', 'BadgerDev', 'password', 'flooddb', 3306);
```

#### _Preparing Statement_

```
$helper->prepare("SELECT city FROM usa WHERE state = ?");
```

#### _Binding params_

```
$helper->bindParams("s", "Wyoming");
```

#### _Handling execution_

```
if($helper->execute()) { /* Returns boolean dependent on if it was executed without error */ 
}
```

#### _Getting number of rows_

```
$helper->num_rows // Can only be ran after execute() function ran
```

#### _Looping through Associative Array_

```
while ($row = $helper->get_both_array_results()) {
        echo 'The city is: ' . $row['city'];
    }
```

### _SQL Objects:_
---

#### _Looping through SQL Object_

```
while($row = $helper->get_results_as_objs()) {
    echo 'One of the IDs to the query is: ' . $row->id;
}
```

#### _Getting the single SQL Object_

```
$helper->get_sql_obj()->id; // This is the ID column value of query
```
