<?php
namespace core\Language;
/**
 * Laabs Query Language Lexer
 *
 * @package Core
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
class Lexer
{
    
    protected $lexemes = array(
            'SWITCH' => '\<\?[A-Z]{3,}\s+.*\?\>',
            'NUMBER' => '\-?\d+(?:\.\d+(e\d+)?)?',
            'DOUBLE QUOTED STRING' => '\"(?:\\\\"|.)*?\"',
            'SINGLE QUOTED STRING' => "'(?:\\\\'|.)*?'",
            'VARIABLE' => '\:[a-zA-Z_][a-zA-Z0-9_]*',
            'OPERATORS' => '[\=\-\+\~\!\>\<\|\.|&|\|]{1,2}',
            //'OPERATORS' => '(\!)?(=|~)',
            //'OPERATORS' => '(=|\!=|\~|\!~|\>|\>\=|\<|\<\=|\|\||\.\.|\:)',
            //'OPERATORS' => '([\!\<\>\.])?([\=\~\.\:\>\<])',
            'STRING' => '\w+',
            'MISC' => '.'
        );
        
    /**
     * Map the constant values with its token type
     *
     * @var array
     */
    private $tokenMap = array(
        '('     => LAABS_T_OPEN_PARENTHESIS,
        ')'     => LAABS_T_CLOSE_PARENTHESIS,
        '['     => LAABS_T_OPEN_BRACKET,
        ']'     => LAABS_T_CLOSE_BRACKET,
        '{'     => LAABS_T_OPEN_BRACE,
        '}'     => LAABS_T_CLOSE_BRACE,
        'AND'   => LAABS_T_AND,
        '&&'    => LAABS_T_AND,
        'OR'    => LAABS_T_OR,
        '||'    => LAABS_T_OR,
        '!'     => LAABS_T_NOT,
        'NOT'   => LAABS_T_NOT,
        '='     => LAABS_T_EQUAL,
        '!='    => LAABS_T_NOT_EQUAL,
        '>'     => LAABS_T_GREATER,
        '>='    => LAABS_T_GREATER_OR_EQUAL,
        '<'     => LAABS_T_SMALLER,
        '<='    => LAABS_T_SMALLER_OR_EQUAL,
        '~'     => LAABS_T_CONTAINS,
        '!~'    => LAABS_T_NOT_CONTAINS,
        '..'    => LAABS_T_RANGE_SEPARATOR,
        '/'     => LAABS_T_NS_SEPARATOR,
        ','     => LAABS_T_LIST_SEPARATOR,
        ':'     => LAABS_T_ASSOC_OPERATOR,
        'TRUE'  => LAABS_T_TRUE,
        'FALSE' => LAABS_T_FALSE,
        'NULL'  => LAABS_T_NULL,
        'READ'  => LAABS_T_READ,
        'CREATE'  => LAABS_T_CREATE,
        'UPDATE'  => LAABS_T_UPDATE,
        'DELETE'  => LAABS_T_DELETE,
        'SORT'    => LAABS_T_SORT,
        'LOCK'    => LAABS_T_LOCK,
        'RETURN'  => LAABS_T_RETURN,
        'OFFSET'  => LAABS_T_OFFSET,
        'LIMIT'   => LAABS_T_LIMIT,
        'UNIQUE'  => LAABS_T_UNIQUE,
        'COUNT'   => LAABS_T_COUNT,
        'SUMMARISE'=> LAABS_T_SUMMARISE,
    );

    /**
     * tokenize a query string
     * @param string $string    The query string
     * @param bool   $withtypes Set token types or simply split
     *
     * @return array
     */
    public function tokenize($string, $withtypes=true)
    {      
        $tokens = new Tokens();

        $matches = preg_split("#(". implode('|', $this->lexemes) .")#", $string, -1, 7);

        foreach ($matches as $match) {
            $lexem = $match[0];
            $offset = $match[1];
            if ($token = $this->getToken($lexem, $offset)) {
                $tokens->push($token);
            }
        }

        $tokens->rewind();
        
        return $tokens;
    }
    
    /**
     * Get the token based on the lexem value
     * @param string $lexem
     * @param int    $offset
     *
     * @return mixed The code
     */
    protected function getToken($lexem, $offset)
    {
        $tokenId = strtoupper($lexem);
        $code = false;
        $value = $lexem;
        switch(true) {
            case isset($this->tokenMap[$tokenId]):
                $code = $this->tokenMap[$tokenId];
                break;
            
            case is_numeric($lexem):
                $code =  LAABS_T_NUMBER;
                break;

            case ctype_space($lexem):
                $code = false;
                break;

            case substr($lexem, 0, 2) == '<?' && substr($lexem, -2) == '?>':
                $type = strtok(substr($lexem, 2, -2), ' ');
                switch ($type) {
                    case 'SQL':
                        $code = LAABS_T_SQL;
                        break;

                    case 'PHP':
                        $code = LAABS_T_PHP;
                        break;

                    case 'XPATH':
                        $code = LAABS_T_XPATH;
                        break;

                    case 'LQL':
                        $code = LAABS_T_LQL;
                        break;
                }
                $value = substr($lexem, strlen($type)+3, -2);
                break;

            case $lexem[0] == ":":
                $code = LAABS_T_VAR;
                break;
            
            default:
                if (($lexem[0] == '"' && $lexem[strlen($lexem)-1] == '"')
                    || ($lexem[0] == "'" && $lexem[strlen($lexem)-1] == "'")) {
                    $code = LAABS_T_ENCLOSED_STRING;
                    break;
                }

                $code = LAABS_T_STRING;
        }

        if ($code) {
            return new Token($code, $value, $offset);
        }
    }


}