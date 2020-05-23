<?php


namespace interfaces;


interface ViewInterface
{
    /**
     * ViewInterface constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration);

    /**
     * @description return template with layout
     * @param string $templateName
     * @param array $variables using in template
     * @return string
     */
    public function render(string $templateName, array $variables) : string ;

    /**
     * @description return template without layout
     * @param string $templateName
     * @param array $variables using in template
     * @return string
     */
    public function renderPartial(string $templateName, array $variables) : string ;

}