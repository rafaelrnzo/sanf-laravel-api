<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

class ${VERB}${NAME}Listener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  \$event
     * @return void
     */
    public function handle(\$event)
    {
        //
    }
}
