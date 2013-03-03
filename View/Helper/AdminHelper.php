<?php

class AdminHelper extends AppHelper {

	/**
	 * Return a list of models grouped by plugin, to use in the navigation menu.
	 *
	 * @return array
	 */
	public function getNavigation() {
		$plugins = array_merge(array('Core'), App::objects('plugins'));
		$navigation = array();

		foreach ($plugins as $plugin) {
			if ($plugin === 'Admin') {
				continue;
			}

			if ($models = $this->getModels($plugin)) {
				foreach ($models as $model) {
					$url = Inflector::underscore($model);

					if ($plugin !== 'Core') {
						$url = Inflector::underscore($plugin) . '.' . $url;
					}

					$navigation[$plugin][] = array(
						'model' => $model,
						'url' => $url
					);
				}
			}
		}

		return $navigation;
	}

	/**
	 * Return a list of available models from the defined plugin.
	 *
	 * @param string $plugin
	 * @return array
	 */
	public function getModels($plugin = null) {
		if ($plugin) {
			$plugin = ($plugin === 'Core') ? '' : $plugin . '.';
		}

		// Fetch models and filter out AppModel's
		$models = array_filter(App::objects($plugin . 'Model'), function($value) {
			return (strpos($value, 'AppModel') === false);
		});

		// Filter out models that don't connect to the database
		foreach ($models as $i => $model) {
			$object = ClassRegistry::init($plugin . $model);

			if (!$object->useTable) {
				unset($models[$i]);
			}
		}

		return $models;
	}

}