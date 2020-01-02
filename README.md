# axe-ci
AXE Scripting Language Translator for Code Igniter (Splint).

AXE is a scripting language used to easily extract information from a large pool
of text using Regular Expressions.

### Installation ###
Download and Install Splint from https://splint.cynobit.com/downloads/splint and run the below from the root of your Code Igniter project.
```bash
splint install cynobit/axe-ci
```

### Usage ###
Top load the library, use
```php
// Load Package
$this->load->package('cynobit/axe-ci');
// Run AXE Script.
$this->axe->run('<path_to_axe_script>', '<large_pool_of_text_or_string>', $data);
// Print/Dump Extracted Data.
var_dump($data);
```

### AXE Script ###
The Language of the AXE Script is quite easy, not too complex, and consists mainly of the function calls below.

| Function                                      | Description                                             |
| --------------------------------------------- | --------------------------------------------------------|
| `AXE`                                         | The function that gave the language its name, I takes 1 argument which is a RegEx pattern describing what should be removed/eliminated from the current pool of text.|
| `CARVE`                                       | This function takes 1 argument which is a RegEx pattern to match in the pool of string/current text buffer and discard the rest. |
| `VERIFY`                                      | This function takes 1 argument which is a RegEx pattern and verifies that the __WHOLE__ current value of the text buffer, matches the given pattern. This function will throw an exception if it fails, i.e If the pattern do not match. |
| `CHECK`                                       | This function takes 1 argument which is a RegEx pattern and checks that given pattaern can find at least a match in the current text buffer. This function will throw an exception if it fails, i.e If the pattern do not match. |
| `PACK`                                        | This function takes 1 argument, which is the key or field name to assign the value of the current text buffer to in its resulting object, __A call to this function resets the text buffer__ so you can `CARVE` or `AXE` something else in order to extract (call `PACK` again). |

AXE Scripts are generally a means to an end, which is to process pools of text as many times as possible, and extract information from them into a key value pair variable. In PHP, you'll end up with an `stdClass` object whose fields are the ones you choose to extract.

### Example AXE Script ###

Let's assume we have the following piece of text to process.
```
Good Morning Sir,
Here are the exam results for your son

English: A1
Mathematics: A1
Chemistry: B2
Physics: A1
Geography: B3
Economics: B2
```
And you want to Extract Mathematics and Geography. You could use the AXE script below

```axe
CHECK("Mathematics:[A-F]{1}[1-9]?")
CHECK("Geography:[A-F]{1}[1-9]?:")

CARVE("Mathematics:[A-F]{1}[1-9]?")
AXE("Mathematics");
PACK("mathematics");

CARVE("Geography:[A-F]{1}[1-9]?")
AXE("Geography");
PACK("geography")
```

When run with the PHP interpreter, you will have an stdClass object with the following structure.

```php
class stdClass {
  public $mathematics = 'A1';
  public $geography = 'B3';
}
```

### Language Config ###
AXE has some configurable options.
