<?php

/**
 * @author	    Dirk Louwers	<dirk_louwers@hotmail.com>
 * @copyright	copyright (c) 	2005 Dirk Louwers
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/kernel/object.php';

/**
 * Class representing a real-estate project.
 * This class will be used to represent the data layer of projects
 * module.
 *
 *
 * @author  Dirk Louwers <dirk_louwers@hotmail.com>
 * @todo    Tests that need to be made:
 *          - full object testing, database transactions
 */
define('PROJ_TYPE_RESIDENTIAL', 1);
define('PROJ_TYPE_COMMERCIAL', 2);
define('PROJ_TYPE_HORECA', 3);

define('PROJ_STATUS_INPROGRESS', 1);
define('PROJ_STATUS_DONE', 2);

class Project extends XoopsObject
{
    /**
     * Array of images that belong to this project
     * @var array
     */

    public $_images = [];

    /**
     * constructor
     *
     * Initiates the attributes of the project
     */
    public function __construct()
    {
        $this->XoopsObject();

        //$this->initVar('dohtml', XOBJ_DTYPE_INT, 0 );

        //$this->initVar('doxcode', XOBJ_DTYPE_INT, 1 );

        //$this->initVar('dosmiley', XOBJ_DTYPE_INT, 0 );

        //$this->initVar('doimage', XOBJ_DTYPE_INT, 1 );

        //$this->initVar('dobr', XOBJ_DTYPE_INT, 1 );

        $this->initVar('project_id', XOBJ_DTYPE_INT, null, false);

        $this->initVar('project_name', XOBJ_DTYPE_TXTBOX, null, true, 255);

        $this->initVar('project_description', XOBJ_DTYPE_TXTAREA, null);

        $this->initVar('project_type', XOBJ_DTYPE_INT, null, true);

        $this->initVar('project_location', XOBJ_DTYPE_TXTBOX, null, false, 255);

        $this->initVar('project_details', XOBJ_DTYPE_TXTAREA, null, false);

        $this->initVar('project_status', XOBJ_DTYPE_INT, null, true);

        $this->initVar('project_salesinfo', XOBJ_DTYPE_TXTAREA, null, false);

        $this->initVar('project_rentinfo', XOBJ_DTYPE_TXTAREA, null, false);

        $this->initVar('project_enrollment', XOBJ_DTYPE_TXTAREA, null, false);

        $this->initVar('project_areacode', XOBJ_DTYPE_TXTBOX, null, false, 10);
    }

    /**
     * Get the images belonging to the project
     *
     * @return array array of images belonging to the project
     */
    public function getImages()
    {
        return $this->_images;
    }

    /**
     * Set the images for the project
     *
     * @param array $imagesArr Array of images that belong to the project
     */
    public function setImages(&$imagesArr)
    {
        if (is_array($imagesArr)) {
            $this->_images = &$imagesArr;
        }
    }
}

/**
 * Class for handling the Project object.
 * This class will be used to handle all database access
 * when working with the project class.
 *
 *
 * @author  Dirk Louwers <dirk_louwers@hotmail.com>
 * @todo    Tests that need to be made:
 *          - full object testing, database transactions
 */
class ProjectHandler extends XoopsObjectHandler
{
    /**
     * constructor
     *
     * Used to initiate the handler's database access
     *
     * @param object $db reference to the {@link XoopsDatabase} object
     */
    public function __construct($db)
    {
        $this->XoopsObjectHandler($db);
    }

    /**
     * Used to return an instance of the class
     *
     * @param object $db reference to the {@link XoopsDatabase} object
     * @return object reference to a {@link ProjectHandler} object
     */
    public function &getInstance($db)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self($db);
        }

        return $instance;
    }

    /**
     * Used to return a new instance of the class
     *
     * @param object $db reference to the {@link XoopsDatabase} object
     * @return object reference to a {@link Project} object
     */
    public function &create($db)
    {
        return new self($db);
    }

    /**
     * Used to retrieve a Project from the database
     *
     * @param int $id id of the project to be retrieved
     * @return object reference to a {@link Project} object, false on error
     */
    public function get($id)
    {
        $id = (int)$id;

        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('projects') . ' WHERE project_id=' . $id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $project = new Project();

                $project->assignVars($this->db->fetchArray($result));

                return $project;
            }
        }

        return false;
    }

    /**
     * Used to store a Project in the database
     *
     * @param \XoopsObject $project {@link Project} object to be stored
     * @return int id of the newly inserted project, true after
     *                              succesfull updating a project or false on failure
     */
    public function insert(XoopsObject $project)
    {
        if ('project' != get_class($project)) {
            return false;
        }

        if (!$project->cleanVars()) {
            return false;
        }

        foreach ($project->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        $project_id = $project->getVar('project_id');

        if (empty($project_id)) {
            $project_id = $this->db->genId('projects_project_id_seq');

            $sql = 'INSERT INTO ' . $this->db->prefix('projects') . ' (project_id, project_name, project_description, project_type, project_location, project_details, project_status, project_salesinfo, project_rentinfo, project_enrollment, project_areacode) VALUES (' . $project_id . ', ' . $this->db->quoteString($project_name) . ', ' . $this->db->quoteString($project_description) . ', ' . $project_type . ', ' . $this->db->quoteString($project_location) . ', ' . $this->db->quoteString($project_details) . ', ' . $project_status . ', ' . $this->db->quoteString($project_salesinfo) . ', ' . $this->db->quoteString($project_rentinfo) . ', ' . $this->db->quoteString($project_enrollment) . ', ' . $this->db->quoteString($project_areacode) . ')';
        } else {
            $sql = 'UPDATE ' . $this->db->prefix('projects') . ' SET project_name=' . $this->db->quoteString($project_name) . ', project_description=' . $this->db->quoteString($project_description) . ', project_type=' . (int)$project_type
                   . ', project_location=' . $this->db->quoteString($project_location) . ', project_details=' . $this->db->quoteString($project_details) . ', project_status=' . (int)$project_status
                   . ', project_salesinfo=' . $this->db->quoteString($project_salesinfo) . ', project_rentinfo=' . $this->db->quoteString($project_rentinfo) . ', project_enrollment=' . $this->db->quoteString($project_enrollment) . ', project_areacode=' . $this->db->quoteString($project_areacode) . ' WHERE project_id=' . (int)$project_id;
        }

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        if (empty($project_id)) {
            $project_id = $this->db->getInsertId();
        }

        $project->assignVar('project_id', $project_id);

        return $project_id;
    }

    /**
     * Used to delete a Project from the database
     *
     * @param \XoopsObject $project {@link Project} object to be deleted
     * @return bool true on success, false on failure
     */
    public function delete(XoopsObject $project)
    {
        if ('project' != get_class($project)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE project_id = %u', $this->db->prefix('projects'), $project->getVar('project_id'));

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Used to get Projects from the database
     *
     * @param null $criteria {@link CritereaElement} conditions to meet
     * @return array of Project objects on success, false on failure
     */
    public function &getObjects($criteria = null)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('projects');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            $sql .= ' ' . $criteria->getSort();

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $project = new Project();

            $project->assignVars($myrow);

            $ret[] = $project;

            unset($project);
        }

        return $ret;
    }

    /**
     * Used to get Projects from the database that have images
     *
     * @param null $criteria {@link CritereaElement} conditions to meet
     * @return array of Project objects on success, false on failure
     */
    public function &getObjectsWithImages($criteria = null)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT DISTINCT projects.project_id AS project_id, project_name, project_description, project_type'
            . ', project_location, project_details, project_status, project_salesinfo, project_rentinfo, project_enrollment'
            . ', project_areacode FROM ' . $this->db->prefix('projects') . ' AS projects, ' . $this->db->prefix('project_images')
            . ' AS images';

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            if ($sql .= ' ' . $criteria->renderWhere()) {
                $sql .= ' AND projects.project_id = images.project_id';
            } else {
                $sql .= ' WHERE projects.project_id = images.project_id';
            }

            $sql .= ' ' . $criteria->getSort();

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $project = new Project();

            $project->assignVars($myrow);

            $ret[] = $project;

            unset($project);
        }

        return $ret;
    }

    /**
     * Used to count the number of Projects in the database
     *
     * @param null $criteria {@link CriteriaElement} conditions to meet
     * @return array true on success, false on failure
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('projects');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->query($sql)) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Used to count the number of Projects in the database
     * having images.
     *
     * @param null $criteria {@link CriteriaElement} conditions to meet
     * @return array true on success, false on failure
     */
    public function getCountWithImages($criteria = null)
    {
        $sql = 'SELECT DISTINCT (projects.project_id) FROM ' . $this->db->prefix('projects') . ' AS projects, '
            . $this->db->prefix('project_images') . ' AS images';

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            if ($sql .= ' ' . $criteria->renderWhere()) {
                $sql .= ' AND projects.project_id=images.project_id';
            } else {
                $sql .= ' WHERE projects.project_id=images.project_id';
            }
        }

        if (!$result = $this->db->query($sql)) {
            return 0;
        }

        $count = $this->db->getRowsNum($result);

        return $count;
    }

    /**
     * Retrieve images belonging to a project
     *
     * @param null|mixed $criteria
     * @return array array of image filenames belonging to the project, FALSE if failed
     */
    public function &getImagesByProject($criteria = null)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('project_images');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            $sql .= ' ' . $criteria->getSort();

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $image = new ProjectImage();

            $image->assignVars($myrow);

            $ret[] = $image;

            unset($image);
        }

        return $ret;
    }
}

/**
 * Class representing an illustration belonging to a project
 *
 * @author Dirk Louwers <dirk_louwers@hotmail.com>
 * @copyright copyright (c) 2005 Dirk Louwers
 */
class ProjectImage extends XoopsObject
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->XoopsObject();

        $this->initVar('image_id', XOBJ_DTYPE_INT, null, false);

        $this->initVar('project_id', XOBJ_DTYPE_INT, null, false);
    }
}

/**
 * Projectimage handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of Projectimage class objects.
 *
 * @author Dirk Louwers <dirk_louwers@hotmail.com>
 * @copyright copyright (c) 2005 Dirk Louwers
 */
class ProjectImageHandler extends XoopsObjectHandler
{
    /**
     * Create a new image belonging to a project
     *
     * @param mixed $db
     * @return object ProjectImage
     */
    public function &create($db)
    {
        return new self($db);
    }

    /**
     * Used to return an instance of the class
     *
     * @param object $db reference to the {@link XoopsDatabase} object
     * @return object reference to a {@link ProjectImageHandler} object
     */
    public function &getInstance($db)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self($db);
        }

        return $instance;
    }

    /**
     * Retrieve an illustration
     *
     * @param int $id ID of the illustration to get
     * @return mixed reference to the object if successful, else FALSE
     */
    public function get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('project_images') . ' WHERE image_id=' . $id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $image = new ProjectImage();

                $image->assignVars($this->db->fetchArray($result));

                return $image;
            }
        }

        return false;
    }

    /**
     * Inserts an illustration in the database
     *
     * @param \XoopsObject $image
     * @return bool TRUE if already in DB or successful, FALSE if failed
     */
    public function insert(XoopsObject $image)
    {
        if ('projectimage' != mb_strtolower(get_class($image))) {
            return false;
        }

        if (!$image->isDirty()) {
            return true;
        }

        if (!$image->cleanVars()) {
            return false;
        }

        foreach ($image->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        $image_id = $image->getVar('image_id');

        if (empty($image_id)) {
            $link_id = $this->db->genId('projects_images_linkid_seq');

            $sql = sprintf('INSERT INTO %s (project_id, image_id) VALUES (%u, %u)', $this->db->prefix('project_images'), (int)$project_id, (int)$image_id);
        } else {
            $sql = sprintf('UPDATE %s SET project_id = %u, uid = %u WHERE image_id = %u', $this->db->prefix('project_images'), (int)$project_id, (int)$image_id);
        }

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        if (empty($image_id)) {
            $image_id = $this->db->getInsertId();
        }

        $image->assignVar('image_id', $image_id);

        return $image_id;
    }

    /**
     * Delete an illustration from the database
     *
     * @param \XoopsObject $image
     * @return bool FALSE if failed
     */
    public function delete(XoopsObject $image)
    {
        if ('projectimage' != mb_strtolower(get_class($image))) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('project_images'), $image->getVar('image_id'));

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve illustrations from the database
     *
     * @param null $criteria  {@link CriteriaElement} conditions to meet
     * @param bool $id_as_key should the ID be used as the array's key?
     * @return array array of references
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('project_images');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $image = new ProjectImage();

            $image->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$image;
            } else {
                $ret[$myrow['link_id']] = &$image;
            }

            unset($image);
        }

        return $ret;
    }

    /**
     * Count how many illustrations meet the conditions
     *
     * @param null $criteria {@link CriteriaElement} conditions to meet
     * @return int
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('project_images');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Delete all illustrations meeting the conditions
     *
     * @param null $criteria {@link CriteriaElement} with conditions to meet
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('project_images');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
