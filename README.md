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

| Function   | Description                                             |
| ---------- | --------------------------------------------------------|
| `AXE`      | The function that gave the language its name, I takes 1 argument which is a RegEx pattern describing what should be removed/eliminated from the current pool of text.|
| `algorithm`      | The Algorithm used to Sign and Verify Tokens. e.g. HS256                                |
