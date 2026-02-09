<?php

namespace LRSDA\Server\Services;

class XapiRegistry
{

    // ---------------------
    // STATIC PUBLIC METHODS
    // ---------------------
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new XapiRegistry();
        }
        return self::$instance;
    }
    // --------------------
    // PROPERTIES
    // --------------------

    const OBJECT_TYPE = "type";
    const OBJECT_IFI_ROOT = "ifi_root";
    const EXTENSION_ID = "extension_id";
    const RESULT_EXTENSION_ID = "result_extension_id";
    const RESULST_EXTENSION_CONTENT = "result_extension_content";

    const STUDENT = 'Students';
    const ACADEMIC = 'Academics';

    const STUDENT_FEED = "Feed";

    protected static $instance;
    protected $registry = [];
    // --------------------
    // PUBLIC METHODS
    // --------------------
    public function __construct()
    {
        $this->registry = json_decode($this->loadPropertiesFile('registry.json'), true);
    }

    /**
     * reruns the requested xAPI type, extension id or content schema
     */
    public function get(string $activity, string $el = "type"): ?string
    {
        $activity = strtolower($activity);
        $key = array_search($activity, array_map('strtolower', array_column($this->registry['activities'], 'name')));
        if ($key === false) {
            error_log(__METHOD__ . " : invalid activity [$activity].");
            return null;
        }
        switch ($el) {
            case 'type':
                return $this->registry['activities'][$key]['type'];
            break;
            case 'ifi_root':
                return $this->registry['activities'][$key]['ifi_root'];
            break;
            case 'extension_id':
                return $this->registry['activities'][$key]['extension']['id'];
            break;
            case 'result_extension_id':
                return $this->registry['activities'][$key]['result']['extension']['id'];
            break;
            case 'result_extension_content':
                return json_encode($this->registry['activities'][$key]['result']['extension']['content']);
            break;

            default:
                error_log(__METHOD__ . " : invalid activity element [$activity :: $el].");
                break;
        }
        return null;
    }

    public function getVerb(string $name, bool $idOnly = false): ?array
    {
        $name = strtolower($name);
        $key = array_search($name, array_map('strtolower', array_column($this->registry['verbs'], 'name')));
        if ($key === false) {
            error_log(__METHOD__ . " : invalid verb [$name].");
            return null;
        }
        $r = $this->registry['verbs'][$key];
        if ($idOnly) {
            return [ 'id' => $r['id'] ];
        }
        unset($r['description']);
        return $r;
    }

    /**
     * reverse lookup on xAPI id to get verb name
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function verbReverseLookup(string $id): string
    {
        foreach ($this->registry['verbs'] as $verb) {
            if ($verb['id'] == $id) {
                return $verb['name'];
            }
        }
        error_log(__METHOD__ . " : invalid xAPI verb id given ["
            . $id
            . "] return [undefined]");
        return 'undefined';
    }

    /**
     * reverse lookup on xAPI id to get activity name
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function activityReverseLookup(string $type): string
    {
        foreach ($this->registry['activities'] as $activity) {
            if ($activity['type'] == $type) {
                return $activity['name'];
            }
        }
        error_log(__METHOD__ . " : invalid xAPI activity type givien ["
            . $type
            . "] return [undefined]");
        return 'undefined';
    }

    /**
     * replace activities and verbs in json query
     *
     * search for __REG::xxx.yyyy__ in text where
     * xxx : element type [activity|verb]
     * yyy : in registry name for element
     * whole string will be replaced by xAPI id
     *
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function replaceInQuery(string $jsonQuery): string
    {
        $jsonQuery = preg_replace_callback(
            '/__REG::(.*)__/',
            function ($matches) {
                $key = explode('.', $matches[1]);
                $value = "";
                switch ($key[0]) {
                    case 'activity':
                        $value = $this->get(ucfirst($key[1]), self::OBJECT_TYPE);
                        break;
                    case 'verb':
                        $value = $this->getVerb($key[1])['id'];
                        break;
                    case 'ext':
                        $value = str_replace(".", "&46;", $this->get(ucfirst($key[1]), self::EXTENSION_ID));
                        break;

                    default:
                        error_log(__METHOD__ . " : invalid object type [" . $key[0] . "]. return empty string.");
                        $value = "";
                        break;
                }

                return $value;
            },
            $jsonQuery
        );
        return $jsonQuery;
    }

    /**
     * returns the registry in a markdown format
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function asMarkdown(): string
    {
        $md = "# SMART/ULLA xAPI Registry \n";
        $md .= "version: " . $this->registry['version'] . "\n \n --- \n";
        $md .= "## Activities / Objects \n";
        // uasort()
        foreach ($this->registry['activities'] as $activity) {
            if (in_array(
                $activity['name'],
                [
                    'GenericObject',
                    'GenericObjectWithResult'
                ]
            )) {
                continue;
            }
            $md .= "### " . $activity['name'] . " \n \n";
            $md .= $activity['description'] . "\n";
            $md .= "- IFI root: " . $activity['ifi_root'] . "\n";
            $md .= "- type: " . $activity['type'] . "\n";
            $md .= "- extension : \n";
            if (isset($activity['extension'])) {
                $md .= "  - id : " . $activity['extension']['id'] . "\n";
                if ($activity['extension']['content'] == null) {
                    $md .= "  - content : free form \n";
                } else {
                    $md .= "  - content : \n";
                    $md .= "```json \n";
                    $md .= json_encode($activity['extension']['content'], JSON_PRETTY_PRINT);
                    $md .= "\n ```\n";
                }
            }
            if (isset($activity['result'])) {
                $md .= "- result : \n";
                if (isset($activity['result']['extension'])) {
                    $md .= "  - extension : \n";
                    $md .= "    - id : " . $activity['result']['extension']['id'] . "\n";
                    if ($activity['result']['extension']['content'] == null) {
                        $md .= "    - content : free form \n";
                    } else {
                        $md .= "    - content : \n";
                        $md .= "```json \n";
                        $md .= json_encode($activity['result']['extension']['content'], JSON_PRETTY_PRINT);
                        $md .= "\n ```\n";
                    }
                }
            }
            $md .= "\n --- \n";
        }

        // verbs
        $md .= "## Verbs \n";
        foreach ($this->registry['verbs'] as $verb) {
            $md .= "### " . $verb['name'] . " \n \n";
            $md .= $verb['description'] . "\n\n";
            $md .= "id : " . $verb['id'] . "\n";
            foreach ($verb['display'] as $key => $display) {
                $md .= "  - " . $key . " : " . $display . "\n";
            }
            $md .= "\n --- \n";
        }
        return $md;
    }

    /**
     * returns the registry in a HTML format
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function asHTML(): string
    {
        $html = '<html><head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<title>SMART xAPI Registry</title>';
        $html .= '<style>' . file_get_contents(__DIR__ . '/registry.css') . '</style>';
        $html .= '</head>';

        $html .= '<body>';
        $html .= '<h1>SMART/ULLA xAPI Registry</h1>';
        $html .= '<p>version: ' . $this->registry['version'] . '</p>';
        $html .= '<p>date: ' . date('c') . '</p>';

        $html .= '<div class="section">';
        //activities
        $html .= '<h2>Activities / Objects </h2>';

        // uasort()
        foreach ($this->registry['activities'] as $activity) {
            if (in_array(
                $activity['name'],
                [
                    'GenericObject',
                    'GenericObjectWithResult'
                ]
            )) {
                continue;
            }
            $html .= '<div class="activity"><div class="name">';

            $html .= '<h3>' . $activity['name'] . '</h3>';
            $html .= '</div><div class="description">';

            $html .= '<p>' . $activity['description'] . '</p>';
            $html .= '<p>IFI root: <span class="ifi_root">' . $activity['ifi_root'] . '</span></p>';
            $html .= '<p>type: <span class="type">' . $activity['type'] . '</span></p>';
            if (isset($activity['extension'])) {
                $html .= '<p>extension</p>';

                $html .= '<ul>';
                $html .= '<li>id :  <span class="id">' . $activity['extension']['id'] . '</span></li>';
                if ($activity['extension']['content'] == null) {
                    $html .= '<li>content : no.</li>';
                } else {
                    $html .= '<li>content :';
                    $html .= '<pre>';
                    $html .= json_encode($activity['extension']['content'], JSON_PRETTY_PRINT);
                    $html .= '</pre>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }

            if (isset($activity['result'])) {
                $html .= '<p>result :</p>';
                $html .= '<ul>';
                if (isset($activity['result']['extension'])) {
                    $html .= '<li>';
                    $html .= '<ul>';
                    $html .= '<li>id :<span class="id">' . $activity['result']['extension']['id'] . '</span></li>';
                    if ($activity['result']['extension']['content'] == null) {
                        $html .= '<li>content : no</li>';
                    } else {
                        $html .= '<li>content :';
                        $html .= '<pre>';
                        $html .= json_encode($activity['result']['extension']['content'], JSON_PRETTY_PRINT);
                        $html .= '</pre>';
                        $html .= '</li>';
                    }
                }
                $html .= '</ul>';
            }
            $html .= '</div></div>';
        }
        $html .= '</div>';

        // verbs
        $html .= '<div class="section">';
        $html .= '<h2>Verbs</h2>';
        foreach ($this->registry['verbs'] as $verb) {
            $html .= '<div class="verb"><div class="name">';

            $html .= '<h3>' . $verb['name'] . '</h3>';
            $html .= '</div><div class="description">';
            $html .= '<p>' . $verb['description'] . '</p>';
            $html .= '<p>id: <span class="id">' . $verb['id'] . '</span></p>';
            $html .= '<p>display</p>';
            $html .= '<ul>';
            foreach ($verb['display'] as $key => $display) {
                $html .= '<li>';
                $html .= $key;
                $html .= '<span class="display">' . $display . '</span>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div></div>';
        }
        $html .= '</div>';
        $html .= '</body></html>';
        return $html;
    }

    /**
     * returns the registry in a json format
     * @since: 2020-05
     * @author: jp.humblet@uliege.be
     */
    public function asJSON(): string
    {
        return json_encode($this->registry);
    }

    /**
     * destroy singleton instance
     * only for testing & code coverge purpose
     */
    public function destroy(): void
    {
        self::$instance = null;
    }


    // --------------------
    // PRIVATE METHODS
    // --------------------

    /**
     * loads json properties file containing the repository description
     */
    private function loadPropertiesFile(String $filename): string
    {
        $reflector = new \ReflectionClass(self::class);
        $path = pathinfo($reflector->getFileName());
        return file_get_contents($path['dirname'] . '/' . $filename);
    }
}
