<?php
/**
 * management of uploaded photos
 *
 * @uses sfThumbnail
 * @uses sfConfig
 * @uses myUtil (REFACTOR ? <= log !== exception throwing ?)
 *
 * @throws Exception
 *
 */
class myUploader {

  private $uploadDir;
  private $vignetteDir;

  private $vignettePrefix;

  private $gd_w_max;
  private $gd_h_max;
  private $v_w_max;
  private $v_h_max;

  private $log;

  public function __construct()
  {
    $this->uploadDir = sfConfig::get('sf_upload_dir');
    $this->vignetteDir = sfConfig::get('sf_vignettes_dir');
    $this->vignettePrefix = sfConfig::get('app_vignette_prefix'); // vignette_prefix

    $dims = sfConfig::get('app_dims_max_list');
    // var_dump($dims);
    $this->gd_w_max = $dims['photos']['w_max'];
    $this->gd_h_max = $dims['photos']['h_max'];
    $this->v_w_max = $dims['vignettes']['w_max'];
    $this->v_h_max = $dims['vignettes']['h_max'];

    $this->log = sfContext::getInstance()->getLogger();
  }

  public function get()
  {
    return $this->uploadDir;
  }

  // upload de fichier avant save/update
  public function doUpload(sfRequest $r, $hasVignette = false)
  {
    // $file = $r->getFile($this->inputFileName);
    $res = false; $secureFileName = '';
    $msg = 'échec upload'; $log = __FUNCTION__ . ' : ';
    do {
      $inputFileNames = $r->getFileNames();

      if (!sizeof($inputFileNames)) break;
      $msg = 'upload reçu';
      $inputFileName = $inputFileNames[0];
      $file = $_FILES[$inputFileName];
      if ($r->hasFileError($inputFileName)) { $msg .= ' > erreur sur upload'; break; }
      $res = true;

      // create secure name if necessary
      // sfLoader::loadHelpers('AAdmin');
      $ext = $this->getExtension($file['name']);
      $secureFileName = $this->strtoSecureFileNameFormat($file['name'], 30);
      $log .= ' ; file : ' . $file['name'] . ' ; secureName : ' . $secureFileName;
      // save file with secure name
      $grandFormat = new sfThumbnail(800, 600, true, false, 50);
      $grandFormat->loadFile($r->getFilePath($inputFileName));
      $uploadDir = $this->uploadDir;
      $uploadRes = $grandFormat->save($uploadDir .DIRECTORY_SEPARATOR. $secureFileName);
      $msg .= 'OK : ' . $r->getFileName($inputFileName) . ' uploadé';
      if ($hasVignette)
      { // create thumbnail
        $thumbnail = new sfThumbnail(150, 150, true, false, 30);
        $thumbnail->loadFile($r->getFilePath($inputFileName));
        $vignetteName = $this->vignettePrefix . $secureFileName;
        $vignettesDir = $this->vignetteDir;
        $this->log->debug('VIGNETTES DIR : ' . $vignettesDir);
        $thumbnail->save($vignettesDir.DIRECTORY_SEPARATOR.$vignetteName);
        $msg .= ' [avec vignette]';
      }
    } while(false);
    owSf::sf_log($log);
    return array('isOk' => $res, 'msg' => $msg, 'secureName' => $secureFileName);
  }
  /**
 * réduire une image à l'affichage en gardant les proportions
 * BUG : pkoi bug si DIRECTORY_SEPARATOR au db de $file ???
 *
 * @return string : width="<int>" height="<int>"
 */
  public static function makeToDim($relpathToFile, array $A_max = array()) {
    // $absPath = realpath('.');
    $newAttr = ''; $log_msg = __FUNCTION__ . ' : ';
    do {
      if ('\\' === substr($relpathToFile, 0, 1) OR '/' === substr($relpathToFile, 0, 1)) {
        $relpathToFile = substr($relpathToFile, 1, strlen($relpathToFile));
      }
      if ( !key_exists('w', $A_max) AND !key_exists('h', $A_max) ) {
        owSf::sf_log($log_msg . ' array doit avoir indice w OR h'); break;
      }
      if ( 1 < sizeof($A_max)) { owSf::sf_log($log_msg . ' w OR h [not both !]'); break; }
      owSf::sf_log('file : ' . $relpathToFile);
      // print sfConfig::get('sf_vignettes_reldir'); exit;
      if (!file_exists($relpathToFile) ) {owSf::sf_log($log_msg . 'file ' . $relpathToFile . ' does not exist !'); break; }
      $t = getimagesize($relpathToFile);
      // no need : file_exists already tested ?
      // if (false === $t) { owSf::sf_log($log_msg . $relpathToFile . ' fichier introuvable'); break; }
      list($w, $h, $mime, $newAttr) = $t;
      if (isset($A_max['w'])) {
        $newWidth = $A_max['w'];
        $newHeight = ($newWidth/$w) * $h;
      } elseif (isset($A_max['h'])) {
        $newHeight = $A_max['h'];
        $newWidth = ($newHeight/$h) * $w;
      }
      $newAttr = 'width="' . $newWidth . '" height="' . $newHeight . '"';
    } while (false);
    return $newAttr;
  }

  public static function makePathToVignette($fichier)
  {
    $relPath = sfConfig::get('sf_upload_reldir') . "/" . $fichier;
    // ENHANCE : create rel_dir function in perso_dev/owSfHelper
    $f = __FUNCTION__ . ' : '; $msg_error = '';
    do {
      $tmpPath = sfConfig::get('sf_vignettes_reldir') . '/' . sfConfig::get('app_vignette_prefix') . $fichier;
      if ( !file_exists($tmpPath) ) {
        $msg_error .= $tmpPath . ' : fichier introuvable !';
        break;
      }
      $relPath = $tmpPath;
    } while (false);
    if ($msg_error) owSf::sf_log($f . $msg_error, 'err');
    return $relPath;
  }

  /**
 * make a filename secure for all platforms + web
 * : no space, no non-utf8 char
 *
 * @param string $str
 * @return string
 */
  protected function strtoSecureFileNameFormat($str, $maxLength = 0)
  {
    if (empty($str)) { throw new Exception(__FUNCTION__ . ' : arg vide !'); }
    // enlever les accents
    $tmp = strtr($str,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ%', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy_');
    // remplacer les caracteres autres que lettres, chiffres et point par _
    $secureFormat= strtolower(preg_replace('/([^.a-z0-9]+)/i', '_', $tmp));
    owSf::sf_log(__FUNCTION__ . ' : ' . $str . ' => ' . $secureFormat);
    if (empty($secureFormat)) { throw new Exception(__FUNCTION__ . ' : arg vide !'); }

    if (0 < $maxLength AND $maxLength < strlen($secureFormat)) {
      $dbt = strlen($secureFormat) - $maxLength;
      $shortened = substr($secureFormat, $dbt, $maxLength);
      $secureFormat = $shortened;
    }

    return $secureFormat;
  }

  protected function formatFileName($fileName, $ajout, $maxLength = 15)
  {
    $db = 0;
    $newName = '';

    do{
      $secureName = $this->strtoSecureFileNameFormat($fileName);
      if ($db) print 'secureName : ' . $secureName . '<br />';
      $A = explode('.', $secureName);
      if ($db) print_r($A);
      $name = $A[0]; $ext = $A[1];
      if (2 > sizeof($A)) { throw new Exception (__FUNCTION__ . ' : pb de césure !'); exit;}
      if ($maxLength < strlen($name)) { $name = substr($name, 0, $maxLength); }
      /*$r = preg_match('/^name(.+)ext$/', $format, $add);
      if (!$r) { $newName = $secureName; break; }
      // print_r($add[1]);
      // . '_' . $photo->getPlace();*/
      $newName = $name . $ajout . '.' . $ext;
    } while (false);

    return $newName;
  }

  protected function getExtension($fileName)
  {
    $ext = strrchr($fileName, '.');
    return substr($ext, 1);
  }
}