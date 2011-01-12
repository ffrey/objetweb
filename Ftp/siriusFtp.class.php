<?php
/*
 */
class siriusFtp
{
	static protected
	$ftp = array(),
	$connection = null;

	static public function init(array $ftp)
	{
		foreach ($ftp AS $ftpName => $d) {
			foreach (array('user', 'pass', 'server', 'path') AS $k) {
				if (!array_key_exists($k, $d) ) {
					throw new Exception (__CLASS__ . ' : ftp data missing : ' . $k . ' for ftp ' . $ftpName);
				}
			}
		}
		self::$ftp = $ftp;
	}
	
	/**
	 * uploads $local_filepath onto sirius ftp + archives $local_filepath
	 *
	 * @param $local_filepath : absolute path + filename
	 * @param $distant_filename : filename only (without any path)
	 */
	static public function move($local_filepath, $distant_filename, $close = true)
	{
	    $db = false;
		$conn = self::connect();
		// UPLOAD du fichier Sigma
		if (!is_readable($local_filepath) ) {
			throw new Exception("Fichier introuvable : $local_filepath");
		}
		$file_size   = filesize($local_filepath);
		$remote_file = self::$ftp['sirius']['path'] . basename($distant_filename);
		if ($db) {
    		var_dump('local', $local_filepath);
		  	var_dump('remote', $remote_file);
		}
		if (!ftp_put($conn, $remote_file, $local_filepath, FTP_ASCII))
		{
			throw new Exception ("PROBLEME DE CHARGEMENT de $local_filepath");
		}

		if ($close) { // Fermeture de la connexion FTP
			return self::close();
		}
		return true;
	} // moveToSirius()

	static public function download($local_filepath, $distant_filename, $close = true)
	{
		$conn = self::connect();
		$remote_file = self::$ftp['sirius']['path_out'] . basename($distant_filename);
		// Tentative de téléchargement du fichier $server_file et sauvegarde dans le fichier $local_file
		if (!ftp_get($conn, $local_filepath, $remote_file, FTP_BINARY)) {
			throw new Exception ("ECHEC DE DOWNLOAD de $remote_file");
		}

		if ($close) { // Fermeture de la connexion
			return self::close();
		}
		return true;
	}

	/**
	 *
	 * @return string path+name of path $local_filepath was saved into
	 */
	public static function archiveFile($local_filepath)
	{
		$sauv_dir  = dirname($local_filepath) . DIRECTORY_SEPARATOR;
		$sauv_dir .= 'sauv';
		if (!is_dir($sauv_dir) ) {
			if(!mkdir($sauv_dir, 0700) ) {
				throw new Exception (sprintf(
				'%s : missing directory : %s',
				__CLASS__, $sauv_dir
				));
			}
		}
		$sauv = $sauv_dir . DIRECTORY_SEPARATOR;
		$sauv .= basename($local_filepath);
		if (!copy($local_filepath, $sauv)) {
			throw new Exception(printf(
			'La copie de %s vers %s a échoué...<br/>\n',
			$local_filepath, $sauv)
			);
		}
		unlink($local_filepath);
		return $sauv;
	}

	public static function archiveDistantFile($distant_filename, $distant_archive_dirname, $close = true)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$conn = self::connect();
		$remote_file        = self::$ftp['sirius']['path_out'] . basename($distant_filename);
		$remote_archive_dir = self::$ftp['sirius']['path_out'] . $distant_archive_dirname . '/';
		if (!self::ftp_is_dir($remote_archive_dir) ) {
			throw new Exception ("Distant archiving error : directory does not exist : $remote_archive_dir");
		}
		$remote_archive     = $remote_archive_dir . basename($distant_filename);
		if ($db) {
			var_dump($origin, $remote_file, 'into', $remote_archive);
		}
		$ok = ftp_rename($conn, $remote_file, $remote_archive);
		if (!$ok) {
			throw new Exception("Could not archive $remote_file into $remote_archive");
		}
		if ($close) {
			self::close();
		}
	}
	
	/*** PROTECTED ***/
	protected static function connect()
	{
	    $db = false;
		$ret = false;
		if (is_resource(self::$connection) ) {
			return self::$connection;
		}
		self::checkInit();
		if ($db) {
		  var_dump(self::$ftp);
		}
		$ftp_user    = self::$ftp['sirius']['user'];
		$ftp_pass    = self::$ftp['sirius']['pass'];
		$ftp_server  = self::$ftp['sirius']['server'];

		if (!$conn = ftp_connect($ftp_server) ) {
			throw new Exception ("Impossible de se connecter à $ftp_server");
		}
		ftp_pasv($conn, true);
		// Identification auprès du serveur FTP
		if (!@ftp_login($conn, $ftp_user, $ftp_pass))
		{
			throw new Exception ("Ne peut pas se connecter en $ftp_user");
		}

		return $conn;
	}

	protected static function close()
	{
		if (is_resource(self::$connection) ) {
			return ftp_close(self::$connection);
		}
		return true;
	}

	/**
	 * @source : http://uk2.php.net/manual/en/function.ftp-nlist.php#webmaster at weltvolk dot de
	 * @param unknown_type $dir
	 */
	static protected function ftp_is_dir($dir) 
	{
		$conn = self::connect();
		if (ftp_chdir($conn, $dir)) {
			ftp_chdir($conn, '..');
			return true;
		} else {
			return false;
		}
	}

	static protected function checkInit()
	{
		if (!count(self::$ftp) ) {
			throw new Exception (__CLASS__ . ' : no ftp connection defined');
		}
	}
}
