<?php

/**
 * Contrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Comvation AG 2007-2015
 * @version   Contrexx 4.0
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Contrexx" is a registered trademark of Comvation AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Search response class
 *
 * Helper class for the search response. Is going to be
 * turned into a JSON object for communcation through ajax.
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author Stefan Heinemann <sh@comvation.com>
 * @package     contrexx
 * @subpackage  module_knowledge
 */

namespace Cx\Modules\Knowledge\Controller;

/**
 * Search response class
 *
 * Helper class for the search response. Is going to be
 * turned into a JSON object for communcation through ajax.
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author Stefan Heinemann <sh@comvation.com>
 * @package     contrexx
 * @subpackage  module_knowledge
 */
class SearchResponse
{
    /**
     * Status code
     *
     * @var int
     */
    public $status = 1;

    /**
     * Response
     *
     * @var string
     */
    public $content = "";
}
