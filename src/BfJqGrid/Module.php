<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE. This software consists of voluntary contributions
 * made by many individuals and is licensed under the MIT license. For more
 * information, see <http://www.doctrine-project.org>.
 */
namespace BfJqGrid;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;

use BfJqGrid\View\Helper;

/**
 * Base module for integration of Doctrine projects with ZF2 applications
 *
 * @license MIT
 * @link http://www.doctrine-project.org/
 * @since 0.1.0
 * @author Kyle Spraggs <theman@spiffyjr.me>
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface, 
    ServiceProviderInterface {
	
	/**
	 * {@inheritDoc}
	 */
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\ClassMapAutoloader' => array (
						__DIR__ . '/autoload_classmap.php' 
				),
				AutoloaderFactory::STANDARD_AUTOLOADER => array (
						StandardAutoloader::LOAD_NS => array (
								__NAMESPACE__ => __DIR__ 
						) 
				) 
		);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'BfJqGrid\View\Helper\JqGrid' => function ($sm) {
							$viewHelper = new View\Helper\JqGrid();
							return $viewHelper;
						} 
				) 
		);
	}
}
