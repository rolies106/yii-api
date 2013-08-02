<?php
    class GeneralFlashMessage extends CWidget
    {
        public function init()
        {
            $flashMessage = Yii::app()->user->getFlashes();

            if (!empty($flashMessage)) {
                $script = "if (typeof $.gritter != 'undefined') {\n ";
                foreach($flashMessage as $key => $message) {
                    $script .= "$.gritter.add({
                                    text: '" . trim(json_encode($message), "\"") . "',
                                    sticky: false,
                                    time: 10000,
                                    class_name: '" . $key . "',
                                    // image: '" . Yii::app()->theme->baseUrl . "/images/warning.png',
                                    before_open: function() {
                                        if ($('.gritter-item-wrapper." . $key . "').length) {
                                            return false;
                                        }
                                    }
                                });";
                }
                $script .= "}";

                Yii::app()->clientScript->registerScript('GeneralFlashMessage', $script, CClientScript::POS_END);
            }

            parent::init();
        }

        public function run()
        {

        }
    }