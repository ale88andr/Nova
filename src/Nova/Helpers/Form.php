<?php

namespace Nova\Helpers;

/**
 * Form helpers
 */
class Form extends Html
{
    /**
     * Method return html text field with name [name can be separated by '.' for ierarchy].
     * Example: Form::text('users.login', ['value' => 'User Login','autocomplete' => 'off'])
     * Return: <input type='text' name='users[login]' id='user_login' class='user_login' value='User Login'
     * autocomplete='off'>
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function text($name, $options = [])
    {
        $name = static ::responseName($name);
        return static ::tag(
            'input',
            false,
            array_merge(
                ['type' => 'text', 'name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html label with name [name can be separated by '.' for ierarchy].
     * Example: Form::label('users.login', 'User login', ['class' => 'login_label'])
     * Return: <label for='user_login' id='user_login' class='login_label'>User login</label>
     *
     * @param string $for       'for' label attribute ['users.login' return user_login]
     * @param string $content   label content
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function label($for, $content, $options = [])
    {
        if (strpos($for, '.')) {
            $for = str_replace('.', '_', $for);
        }

        return static ::tag('label', $content, array_merge(['for' => $for], $options));
    }

    /**
     * Method return html password field with name [name can be separated by '.' for ierarchy].
     * Example: Form::password('users.password', ['autocomplete' => 'off'])
     * Return: <input type='password' name='users[password]' id='user_password' class='user_password' autocomplete='off'>
     *
     * @param string $name      Name attribute ['users.login' return users['login']
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function password($name, $options = [])
    {
        $name = static ::responseName($name);
        return static ::tag(
            'input',
            false,
            array_merge(
                ['type' => 'password', 'name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html email field with name [name can be separated by '.' for ierarchy].
     * Example: Form::email('users.email', ['require' => true])
     * Return: <input type='email' name='users[email]' id='user_email' class='user_email' required >
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function email($name, $options = [])
    {
        $name = static ::responseName($name);
        return static ::tag(
            'input',
            false,
            array_merge(
                ['type' => 'email', 'name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html textarea with name [name can be separated by '.' for ierarchy].
     * Example: Form::textarea('users.about', 'Textarea content', ['cols' => 10])
     * Return: <textarea name='users[about]' class='user_about' id='user_about' cols=10>
     *          Textarea content
     *         </textarea>
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function textarea($name, $content, $options = [])
    {
        $name = static ::responseName($name);
        return static ::tag(
            'textarea',
            $content,
            array_merge(
                ['name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html number field with name [name can be separated by '.' for ierarchy].
     * Example: Form::numeric('users.age', 0, 99, 1)
     * Return: <input type='number' name='users[age]' id='user_age' class='user_age' min=0 max=99 step=1 >
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param string $min       min attribute
     * @param string $max       max attribute
     * @param string $step      step attribute
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function numeric($name, $min, $max, $step, $options = [])
    {
        $name = static ::responseName($name);
        return static ::tag(
            'input',
            false,
            array_merge(
                ['type' => 'number', 'name' => $name['script'], 'min' => $min, 'max' => $max, 'step' => $step, 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html submit field with name.
     * Example: Form::submit('Register', ['id' => 'register'])
     * Return: <input type='submit' id='register' value='Register'>
     *
     * @param string $value     Value attribute
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function submit($value, $options = [])
    {
        return static ::tag('input', false, array_merge(['type' => 'submit', 'value' => $value], $options));
    }

    /**
     * Method return html select with name [name can be separated by '.' for ierarchy].
     * Example: Form::select('users.country', ['Russia', 'Ukraine', 'China'])
     * Return: <select name="users[country]" id="user_country" class="user_country">
     *          <option value="russia">Russia</option>
     *          <option value="ukraine">Ukraine</option>
     *          <option value="china">China</option>
     *         </select>
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $values     Array with select values
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function select($name, $values = [], $options = [])
    {
        $name = static ::responseName($name);
        $content = '';
        foreach($values as $value) {
            $content.= '<option value=' . strtolower($value) . '>' . $value . '</option>';
        }

        return static ::tag(
            'select',
            $content,
            array_merge(
                ['name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options
            )
        );
    }

    /**
     * Method return html checkbox with name [name can be separated by '.' for ierarchy].
     * Example: Form::check_box('users.sex', ['male' => 'Man', 'female' => 'Women'], 'male', [], true)
     * Return: <div style="{display: block}">
     *          <input type="checkbox" name="users[sex]" id="user_sex_male" class="user_sex" value='male' checked="">Man
     *          <input type="checkbox" name="users[sex]" id="user_sex_female" class="user_sex" value='female'>Women
     *         </div>
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $values     Array with checkboxes values
     * @param mixed $checked    Checked by default
     * @param array $options    Html elements attributes
     * @param bool $inline      Inline or Block position
     * @return string (html)
     */
    public static function check_box($name, $values, $checked = false, $options = [], $inline = false)
    {
        $name = static ::responseName($name);
        $html = '<div style="{display: block}">';
        foreach($values as $key => $value) {
            $checked_flag = [];

            if ($checked !== false && $key == $checked) {
                $checked_flag = ['checked' => true];
            }

            $html.= static ::tag(
                'input',
                false,
                array_merge(
                    ['type' => 'checkbox', 'value' => $key, 'name' => $name['script'], 'id' => $name['html'] . '_' . $key, 'class' => $name['html']],
                    $options,
                    $checked_flag
                )
            );

            $html.= $value;

            if ($inline === false) {
                $html.= static ::tag('br');
            }
        }

        return $html . '</div>';
    }

    /**
     * Method return html radio button with name [name can be separated by '.' for ierarchy].
     * Example: Form::radio('users.sex', ['male' => 'Man', 'female' => 'Women'], 'male')
     * Return: <div style="{display: block}">
     *          <input type="radio" name="users[sex]" id="user_sex_male" class="user_sex" value='male' checked>Man
     *          <br>
     *          <input type="radio" name="users[sex]" id="user_sex_female" class="user_sex" value='female'>Women
     *          <br>
     *          </div>
     *
     * @param string $name      Name string ['users.login' return users['login']
     * @param array $buttons    Array with radio values
     * @param mixed $checked    Checked by default
     * @param array $options    Html elements attributes
     * @param bool $inline      Inline or Block position
     * @return string (html)
     */
    public static function radio($name, $buttons, $checked = false, $options = [], $inline = false)
    {
        $name = static ::responseName($name);
        $html = '<div style={display: block}>';
        foreach($buttons as $key => $value) {
            $checked_flag = [];
            if ($checked !== false && $key == $checked) {
                $checked_flag = ['checked' => true];
            }

            $html.= static ::tag(
                'input',
                false,
                array_merge(
                    ['type' => 'radio', 'name' => $name['script'], 'value' => $key, 'id' => $name['html'] . '_' . $key, 'class' => $name['html']],
                    $options,
                    $checked_flag
                )
            );

            $html.= $value;

            if ($inline === false) {
                $html.= static ::tag('br');
            }
        }

        return $html . '</div>';
    }

    /**
     * Method return html hidden field with name.
     * Example: Form::hidden('users.id', 5)
     * Return: <input type='hidden' id='user_id' value=5>
     *
     * @param string $name      Nmae attribute
     * @param string $value     Value attribute
     * @return string (html)
     */
    public static function hidden($name, $value)
    {
        $name = static ::responseName($name);
        return static ::tag('input', false, ['type' => 'hidden', 'value' => $value]);
    }

    /**
     * Method return html date field with name.
     * Example: Form::date('users.born', '1940-01-01')
     * Return: <input type='date' id='user_born' class='user_born' value='01-01-1940'>
     *
     * @param string $name      Name attribute
     * @param string $default   Default date attribute
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function date($name, $default = false, $options = [])
    {
        $name = static ::responseName($name);
        $post_options = [];
        if ($default !== false) {
            $post_options['value'] = $default;
        }

        return static ::tag(
            'input',
            false,
            array_merge(
                ['type' => 'date', 'name' => $name['script'], 'id' => $name['html'], 'class' => $name['html']],
                $options,
                $post_options
            )
        );
    }

    /**
     * Method return array of element names(for html and script).
     *
     * @param string $name      Name string
     * @return array
     */
    protected static function responseName($name)
    {
        $name = trim($name);
        if (strpos($name, '.')) {
            $array_name = explode('.', $name);
            $value_for_attributes = implode('_', $array_name);
            $value_for_name = Hash::extract($array_name);
            foreach($array_name as $value) {
                $value_for_name .= '[' . $value . ']';
            }

            return ['html' => $value_for_attributes, 'script' => $value_for_name];
        }

        return ['html' => $name, 'script' => $name];
    }
}