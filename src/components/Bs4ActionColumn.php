<?php
    namespace unique\yii2bs4helpers\components;

    use Yii;
    use yii\bootstrap4\Html;
    use yii\helpers\Url;

    /**
     * Warning! You must load Bootstrap icons asset yourself.
     * One way of doing this could be add the following to your layout:
     * ```
     * <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
     * ```
     */
    class Bs4ActionColumn extends \yii\grid\ActionColumn {

        /**
         * @var bool If set to true, buttons will be generated, otherwise simple links with icons for text.
         */
        public bool $as_buttons = true;

        /**
         * @var bool If set to true, adds btn-sm class to the buttons
         */
        public bool $use_small = false;

        /**
         * @var string A string to be prepended to every icon class
         */
        public string $prepend_icon_class = 'bi bi-';

        /**
         * Contrary to the default urlCreator() allows to set urls for individual buttons.
         * Structure:
         * [
         *      (string) Button name => (Closure) Render URL,
         *      ...
         * ]
         *
         * Closure definition:
         * ```php```
         * function ( $model, $key, $index ):array|string;
         * ```php```
         *
         * @var array
         */
        public array $url = [];

        /**
         * Allows to easily add aditional button definitions.
         * Structure:
         * [
         *      (string) ButtonName => [
         *          'url' => (string|array|Closure) {@see $url} for details,
         *          'icon' => (string) BS4 icon's class for the button, without the 'bi' part. For example: 'bi bi-eye-fill' would just be 'eye-fill',
         *          'additionalOptions' => (array) Any additional HTML attributes for the button,
         *      ],
         *      ...
         * ]
         *
         * If no 'url' attribute is given for a button, then the button will not be output.
         * @var array
         */
        public array $additional_buttons = [];

        /**
         * @inheritdoc
         */
        protected function initDefaultButtons() {

            $this->initDefaultButton( 'view', 'eye-fill' );
            $this->initDefaultButton( 'update', 'pencil-fill' );
            $this->initDefaultButton( 'delete', 'trash-fill', [
                'data-confirm' => Yii::t( 'yii', 'Are you sure you want to delete this item?' ),
                'data-method' => 'post',
            ] );

            foreach ( $this->additional_buttons as $name => $button ) {

                $url = $button['url'] ?? null;
                if ( $url ) {

                    $this->initDefaultButton(
                        $name,
                        $button['icon'] ?? null,
                        $button['additionalOptions'] ?? []
                    );

                    $this->url[ $name ] = $url;
                }
            }
        }

        /**
         * @inheritdoc
         */
        protected function initDefaultButton( $name, $iconName, $additionalOptions = [] ) {

            if ( !isset( $this->buttons[ $name ] ) && strpos( $this->template, '{' . $name . '}' ) !== false ) {

                $this->buttons[ $name ] = function ( $url, $model, $key ) use ( $name, $iconName, $additionalOptions ) {

                    $class = 'btn btn-default';

                    switch ( $name ) {

                        case 'view':
                            $title = Yii::t( 'yii', 'View' );
                            $class = 'btn btn-primary';
                            break;
                        case 'update':
                            $title = Yii::t( 'yii', 'Update' );
                            $class = 'btn btn-success';
                            break;
                        case 'delete':
                            $title = Yii::t( 'yii', 'Delete' );
                            $class = 'btn btn-danger';
                            break;
                        default:
                            $title = ucfirst( $name );
                    }

                    if ( $this->use_small ) {

                        $class .= ' btn-sm';
                    }

                    $options = array_merge( [
                        'title' => $title,
                        'aria-label' => $title,
                        'data-pjax' => '0',
                    ], $additionalOptions, $this->buttonOptions[ $name ] ?? $this->buttonOptions );

                    $icon = Html::tag( 'i', '', [ 'class' => $this->prepend_icon_class . $iconName ] );
                    if ( $this->as_buttons ) {

                        $options['class'] = ( $options['class'] ?? '' ) . ' ' . $class;
                    }

                    return Html::a( $icon, $url, $options );
                };
            }
        }

        /**
         * @inheritdoc
         */
        public function createUrl( $name, $model, $key, $index ) {

            if ( isset( $this->url[ $name ] ) && is_callable( $this->url[ $name ] ) ) {

                $url = call_user_func( $this->url[ $name ], $model, $key, $index );
                if ( is_array( $url ) ) {

                    $url = Url::toRoute( $url );
                }

                return $url;
            }

            return parent::createUrl( $name, $model, $key, $index );
        }
    }