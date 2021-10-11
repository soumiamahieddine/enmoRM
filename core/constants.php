<?php
/**
 * Laabs core constants definition
 * @package core
 */

/* Components */
// Directories
const LAABS_WEB         = 'web';
const LAABS_TMP         = 'tmp';
const LAABS_CORE        = 'core';
const LAABS_CONF        = 'conf';

const LAABS_APP         = 'app';
const LAABS_DATA        = 'data';

// Base 
const LAABS_RESPONSE    = 'Response';
const LAABS_REQUEST     = 'Request';

const LAABS_INTERFACE   = "Interface";
const LAABS_SERVICE     = 'Service';
    const LAABS_METHOD  = 'Method';
const LAABS_RESOURCE    = 'Resources';
const LAABS_OBSERVER    = 'Observer';
    const LAABS_HANDLER = "handler";

// Dependencies
const LAABS_DEPENDENCY  = 'dependency';
const LAABS_ADAPTER     = 'Adapter';

// Source
const LAABS_SRC         = 'src';
    const LAABS_EXTENSION   = 'ext';

    const LAABS_BUNDLE              = 'bundle';
        const LAABS_API             = 'Api';
            const LAABS_SERVICE_PATH= 'Path';
        const LAABS_PARSER          = 'Parser';
            const LAABS_INPUT       = 'Input';
        const LAABS_SERIALIZER      = 'Serializer';
            const LAABS_OUTPUT      = "Output";
        const LAABS_CONTROLLER      = 'Controller';
            const LAABS_ACTION      = 'Action';
        const LAABS_MODEL           = 'Model';
            const LAABS_PROPERTY    = 'Property';
        const LAABS_BATCH           = 'Batch';
        const LAABS_JOB             = 'job';
            const LAABS_STEP        = 'step';

    const LAABS_PRESENTATION        = 'presentation';
        const LAABS_USER_STORY      = 'UserStory';
            const LAABS_USER_COMMAND= 'UserCommand';
        const LAABS_COMPOSER        = 'Composer';
            const LAABS_MESSAGE     = 'Message';
        const LAABS_PRESENTER       = 'Presenter';
            const LAABS_VIEW        = 'View';


/* Events subjects */
/* Container */
// N/A

/* Routes */
const LAABS_REQUEST_ROUTE = "RequestRoute";
const LAABS_SERVICE_ROUTE = "ServiceRoute";
const LAABS_ACTION_ROUTE  = "ActionRoute";
const LAABS_INPUT_ROUTE   = "InputRoute";
const LAABS_OUTPUT_ROUTE  = "OutputRoute";

/* Base */
const LAABS_SERVICE_INJECTION   = 'ServiceInjection';
const LAABS_SERVICE_OBJECT      = 'ServiceObject';

const LAABS_METHOD_CALL         = 'MethodCall';
const LAABS_METHOD_RETURN       = 'MethodReturn';

/* Presentation */
const LAABS_COMMAND_RETURN      = "CommandReturn";

const LAABS_INPUT_COMPOSITION   = "InputComposition";
const LAABS_INPUT_MESSAGE       = "InputMessage";

const LAABS_VIEW_PRESENTATION   = "ViewPresentation";
const LAABS_VIEW_CONTENTS       = "ViewContents";

/* Bundle */
const LAABS_ACTION_CALL         = 'ActionCall';
const LAABS_ACTION_RESPONSE     = 'ActionResponse';

const LAABS_INPUT_PARSING       = 'InputParsing';
const LAABS_INPUT_ARGUMENTS     = 'InputArguments';

const LAABS_OUTPUT_SERIALIZE    = 'OutputSerialize';
const LAABS_OUTPUT_STREAM       = 'OutputStream';

const LAABS_SERVICE_CALL        = 'ServiceCall';
const LAABS_SERVICE_RETURN      = 'ServiceReturn';

const LAABS_JOB_CREATE          = "JobCreation";
const LAABS_JOB_EXEC            = "JobExecution";
const LAABS_JOB_RESULT          = "JobResult";
const LAABS_STEP_EXEC           = "StepExecution";
const LAABS_STEP_RESULT         = "StepResult";


/* Error / Exception */
const LAABS_ERROR = 'Error';
const LAABS_BUSINESS_EXCEPTION = 'BusinessLogicException';
const LAABS_EXCEPTION = 'Exception';

/* Buffer control */
const LAABS_BUFFER_NONE = 0;
const LAABS_BUFFER_GET = 1;
const LAABS_BUFFER_CLEAN = 2;

/* List separators */
const LAABS_CONF_LIST_SEPARATOR = ';';
const LAABS_NS_SEPARATOR = '\\';
const LAABS_URI_SEPARATOR = '/';

/* Request Arguments */
const LAABS_URI_ARG_SEPARATOR = "&";
const LAABS_URI_ARG_OPERATOR = "=";
const LAABS_CLI_ARG_SEPARATOR = " ";
const LAABS_CLI_ARG_OPERATOR = "=";

/* Patterns */
const LAABS_VARIABLE_PATTERN = '#[\w]*:[\w/]+#';

/* Job control */
const LAABS_BATCH_STATUS_STARTED = "Started";
const LAABS_BATCH_STATUS_FAILED = "Failed";
const LAABS_BATCH_STATUS_COMPLETED = "Completed";

/* Authentication mode */
const LAABS_BASIC_AUTH = "basic";
const LAABS_DIGEST_AUTH = "digest";
const LAABS_APP_AUTH = "app";
const LAABS_REMOTE_AUTH = "remote";

// TOKENS
// Operations
const LAABS_T_READ    = "T_READ";
const LAABS_T_CREATE  = "T_CREATE";
const LAABS_T_UPDATE  = "T_UPDATE";
const LAABS_T_DELETE  = "T_DELETE";
const LAABS_T_COUNT   = "T_COUNT";
const LAABS_T_SUMMARISE = "T_SUMMARISE";

// Switches
const LAABS_T_UNIQUE  = "T_UNIQUE";
const LAABS_T_SORT    = "T_SORT";
const LAABS_T_LOCK    = "T_LOCK";
const LAABS_T_RETURN  = "T_RETURN";
const LAABS_T_OFFSET  = "T_OFFSET";
const LAABS_T_LIMIT   = "T_LIMIT";

    // Separators and operator chars
const LAABS_T_RANGE_SEPARATOR = "RANGE_SEPARATOR"; // Range separator between two value expressions : ..
const LAABS_T_LIST_SEPARATOR = "T_LIST_SEPARATOR"; // List separator  : ,
const LAABS_T_ASSOC_OPERATOR = "ASSOC_OPERATOR";   // Object/Associative operator : :
const LAABS_T_NS_SEPARATOR = "NS_SEPARATOR";       // Namespace separator : /

    
const LAABS_T_OPEN_BRACKET = "T_OPEN_BRACKET";            // Beginning of a list : [
const LAABS_T_CLOSE_BRACKET = "T_CLOSE_BRACKET";          // End of a list : ]
const LAABS_T_OPEN_BRACE = "T_OPEN_BRACE";                // Beginning of an object : { 
const LAABS_T_CLOSE_BRACE = "T_CLOSE_BRACE";              // End of an object : }
const LAABS_T_OPEN_PARENTHESIS = "T_OPEN_PARENTHESIS";    // Beginning of an expression to parse : (
const LAABS_T_CLOSE_PARENTHESIS = "T_CLOSE_PARENTHESIS";  // End of an expression to parse : )
    
    // Comparison operators
const LAABS_T_EQUAL = "T_EQUAL";                          // Comparison operator is equal: =
const LAABS_T_NOT_EQUAL = "T_NOT_EQUAL";                  // Comparison operator is equal: =
const LAABS_T_GREATER = "T_GREATER";                      // Comparison operator is greater than : >
const LAABS_T_GREATER_OR_EQUAL = "T_GREATER_OR_EQUAL";    // Comparison operator is greater or equal : >=
const LAABS_T_SMALLER = "T_SMALLER";                      // Comparison operator is smaller than : <
const LAABS_T_SMALLER_OR_EQUAL = "T_SMALLER_OR_EQUAL";    // Comparison operator is smaller or equal : <=
const LAABS_T_CONTAINS = "T_CONTAINS";                    // Comparison operator contains : ~ or = with * in operand 
const LAABS_T_NOT_CONTAINS = "T_NOT_CONTAINS";            // Comparison operator contains : ~ or = with * in operand 
const LAABS_T_IN = "T_IN";                                // Comparison operator in list = []
const LAABS_T_NOT_IN = "T_NOT_IN";                        // Comparison operator in list != []
const LAABS_T_BETWEEN = "T_BETWEEN";                      // Comparison operator between : = from..to
const LAABS_T_NOT_BETWEEN = "T_NOT_BETWEEN";               // Comparison operator between : !=from..to

    // Logical operators
const LAABS_T_AND = "T_AND";
const LAABS_T_OR = "T_OR";
const LAABS_T_NOT = "T_NOT";

const LAABS_T_ASSERT = "T_ASSERT";

    // arythmetic operators
const LAABS_T_PLUS = "T_PLUS";
const LAABS_T_MINUS = "T_MINUS";
const LAABS_T_MULT = "T_MULT";
const LAABS_T_DIV = "T_DIV";


    // Operands and words
const LAABS_T_NUMBER = "T_NUMBER";
const LAABS_T_STRING = "T_STRING";
const LAABS_T_ENCLOSED_STRING = "T_ENCLOSED_STRING";
const LAABS_T_VAR = "T_VAR";
const LAABS_T_BOOLEAN = "T_BOOLEAN";
const LAABS_T_PROPERTY = "T_PROPERTY";
const LAABS_T_FUNC = "T_FUNC";
const LAABS_T_LIST = "T_LIST";
const LAABS_T_METHOD = "T_METHOD";
const LAABS_T_PATH = "T_PATH";
const LAABS_T_DATE = "T_DATE";

const LAABS_T_NONE = "T_NONE";
const LAABS_T_TRUE = "T_TRUE";
const LAABS_T_FALSE = "T_FALSE";
const LAABS_T_NULL = "T_NULL";

    // Functions
//const LAABS_T_IS_NULL = "T_IS_NULL";

    // Orders
const LAABS_T_ASC = "T_ASC";
const LAABS_T_DESC = "T_DESC";

// Switches
const LAABS_T_XPATH = 'T_XPATH';
const LAABS_T_SQL = 'T_SQL';
const LAABS_T_PHP = 'T_PHP';
const LAABS_T_LQL = 'T_LQL';


// PARSER V2
// Lexer tokens
const LAABS_T_COMPARISON_OPR = 'LAABS_T_COMPARISON_OPR';
const LAABS_T_LOGICAL_OPR = 'LAABS_T_LOGICAL_OPR';

// Language codes
const LAABS_EQ = 'LAABS_EQ';
const LAABS_NE = 'LAABS_NE';
const LAABS_GT = 'LAABS_GT';
const LAABS_GE = 'LAABS_GE';
const LAABS_LT = 'LAABS_LT';
const LAABS_LE = 'LAABS_LE';

const LAABS_CONTAINS = 'LAABS_CONTAINS';
const LAABS_IN = 'LAABS_IN';
const LAABS_BETWEEN = 'LAABS_BETWEEN';

const LAABS_AND = 'LAABS_AND';
const LAABS_OR = 'LAABS_OR';
const LAABS_NOT = 'LAABS_NOT';
const LAABS_XOR = 'LAABS_XOR';
const LAABS_NOR = 'LAABS_NOR';
const LAABS_NAND = 'LAABS_NAND';
const LAABS_XNOR = 'LAABS_XNOR';

const LAABS_IN_COOKIE = 'cookie';
const LAABS_IN_HEADER = 'header';
const LAABS_IN_QUERY = 'query';