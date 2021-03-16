<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;

class ProjectConfig extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'project_config';
		$model = [
			'id' => [
				'type'    => OModel::PK,
				'comment' => 'Id único para cada proyecto'
			],
			'id_project' => [
				'type'     => OModel::NUM,
				'nullable' => false,
				'comment'  => 'Id del proyecto al que pertenece la configuración',
				'ref'      => 'project.id'
			],
			'db_host' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Host de la base de datos'
			],
			'db_user' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Nombre de usuario para la base de datos'
			],
			'db_pass' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'Contraseña cifrada para la base de datos'
			],
			'db_name' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Nombre de la base de datos'
			],
			'db_charset' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Charset de la base de datos'
			],
			'db_collate' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Collate de la base de datos'
			],
			'cookies_prefix' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => true,
				'comment'  => 'Prefijo para las cookies'
			],
			'cookies_url' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'URL para las cookies'
			],
			'base_url' => [
				'type'     => OModel::TEXT,
				'size'     => 250,
				'nullable' => true,
				'comment'  => 'URL base de la aplicación'
			],
			'admin_email' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'Dirección email para notificaciones al admin'
			],
			'default_title' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'Título por defecto para las páginas'
			],
			'lang' => [
				'type'     => OModel::TEXT,
				'size'     => 10,
				'nullable' => true,
				'comment'  => 'Código de idioma por defecto'
			],
			'error_403' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'URL al que redirigir en caso de error 403'
			],
			'error_404' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'URL al que redirigir en caso de error 404'
			],
			'error_500' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => true,
				'comment'  => 'URL al que redirigir en caso de error 500'
			],
			'created_at' => [
				'type'    => OModel::CREATED,
				'comment' => 'Fecha de creación del registro'
			],
			'updated_at' => [
				'type'    => OModel::UPDATED,
				'comment' => 'Fecha de última modificación del registro'
			]
		];

		parent::load($table_name, $model);
	}

	/**
	 * Lista de configuraciones del proyecto
	 */
	private ?array $configuration_lists = null;

	/**
	 * Obtiene la lista de configuraciones de un proyecto
	 *
	 * @return array Lista de configuraciones
	 */
	public function getProjectConfigurationLists(): array {
		if (is_null($this->configuration_lists)) {
			$this->loadProjectConfigurationLists();
		}
		return $this->configuration_lists;
	}

	/**
	 * Guarda la lista de configuraciones de un proyecto
	 *
	 * @param array $lists Lista de configuraciones
	 *
	 * @return void
	 */
	public function setProjectConfigurationLists(array $lists): void {
		$this->configuration_lists = $lists;
	}

	/**
	 * Carga la lista de configuraciones de un proyecto
	 *
	 * @return void
	 */
	private function loadProjectConfigurationLists(): void {
		$sql = "SELECT * FROM `project_config_list_item` WHERE `id_project_config` = ?";
		$this->db->query($sql, [$this->get('id')]);
		$lists = ['css' => [], 'css_ext' => [], 'js' => [], 'js_ext' => [], 'libs' => [], 'extra' => [], 'dir' => []];

		while ($res = $this->db->next()) {
			$prcli = new ProjectConfigListItem();
			$prcli->update($res);

			switch ($prcli->get('type')) {
				case 0: { array_push($lists['css'], '"'.urlencode($prcli->get('value')).'"'); }
				break;
				case 1: { array_push($lists['css_ext'], '"'.urlencode($prcli->get('value')).'"'); }
				break;
				case 2: { array_push($lists['js'], '"'.urlencode($prcli->get('value')).'"'); }
				break;
				case 3: { array_push($lists['js_ext'], '"'.urlencode($prcli->get('value')).'"'); }
				break;
				case 4: { array_push($lists['extra'], ['key' => urlencode($prcli->get('key')), 'value' => urlencode($prcli->get('value'))]); }
				break;
				case 5: { array_push($lists['libs'], '"'.urlencode($prcli->get('value')).'"'); }
				break;
				case 6: { array_push($lists['dir'], ['key' => urlencode($prcli->get('key')), 'value' => urlencode($prcli->get('value'))]); }
				break;
			}
		}

		$this->setProjectConfigurationLists($lists);
	}
}