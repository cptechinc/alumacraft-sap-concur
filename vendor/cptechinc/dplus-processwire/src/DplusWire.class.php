<?php 
	namespace Dplus\ProcessWire;
	
	/**
	 * Static Class that will load Processwire's wire() function into my classes
	 * so that php-integrator will catch it and not mark function calls as errors
	 */
	class DplusWire {
	    /**
	     * Store the wire.
	     * @var mixed
	     */
	    protected static $wire = null;

	    /**
	     * Store the wire if not already stored and then call it.
	     *
	     * @return mixed
	     */
	    public static function wire()
	    {
	        if (is_null(static::$wire)) {
	            foreach (["\\ProcessWire\\wire", 'wire'] as $wire) {
	                if (function_exists($wire)) {
	                    static::$wire = $wire;
	                }
	            }
	        }

	        return call_user_func_array(static::$wire, func_get_args());
	    }
	}
