<?php

/**
 * mock class used to simulate mail sending
 * only method ->send() is overridden
 * @uses myUtil
 * @throws Exception
 */

if (!class_exists('Swift') ) { require_once dirname(__FILE__) . '\vendor\Swift.php'; }
class mockSwift extends Swift
{
  protected $subject       = 'default Subject';
  protected $from          = 'default From';
  protected $myOutput      = 'default Output';

  /*** OVERRIDDEN ***/

  /**
   * asks factorMailer to write a file instead of sending a mail
   *
   * @see parent::send() !
   */
  public function send(Swift_Message $message, $recipients, $from)
  {
    Swift_ClassLoader::load("Swift_Message_Encoder");
    if (is_string($recipients) && preg_match("/^" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . "\$/", $recipients))
    {
      $recipients = new Swift_Address($recipients);
    }
    elseif (!($recipients instanceof Swift_AddressContainer))
    throw new Exception("The recipients parameter must either be a valid string email address, ".
    "an instance of Swift_RecipientList or an instance of Swift_Address.");

    if (is_string($from) && preg_match("/^" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . "\$/", $from))
    {
      $from = new Swift_Address($from);
    }
    elseif (!($from instanceof Swift_Address))
    throw new Exception("The sender parameter must either be a valid string email address or ".
    "an instance of Swift_Address.");

    $log = Swift_LogContainer::getLog();

    if (!$message->getEncoding() ) // && !$this->connection->hasExtension("8BITMIME")
    {
      $message->setEncoding("QP", true, true);
    }

    $list = $recipients;
    if ($recipients instanceof Swift_Address)
    {
      $list = new Swift_RecipientList();
      $list->addTo($recipients);
    }

    Swift_ClassLoader::load("Swift_Events_SendEvent");
    $send_event = new Swift_Events_SendEvent($message, $list, $from, 0);

    $this->notifyListeners($send_event, "BeforeSendListener");

    $to = $cc = array();
    if (!($has_from = $message->getFrom())) $message->setFrom($from);
    if (!($has_return_path = $message->getReturnPath())) $message->setReturnPath($from->build(true));
    if (!($has_reply_to = $message->getReplyTo())) $message->setReplyTo($from);
    if (!($has_message_id = $message->getId())) $message->generateId();

    $this->command("MAIL FROM: " . $message->getReturnPath(true), 250);

    $failed = 0;
    $sent = 0;
    $tmp_sent = 0;

    $it = $list->getIterator("to");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $to[] = $address->build();
      try {

        // - $this->command("RCPT TO: " . $address->build(true), 250);
        factorMailer::fakeMail(array(), '', '', "RCPT TO: " . $address->build(true)); // +

        $tmp_sent++;
      } catch (Swift_BadResponseException $e) {
        /*
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
        */
      }
    }
    $it = $list->getIterator("cc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $cc[] = $address->build();
      try {
        $this->command("RCPT TO: " . $address->build(true), 250);
        $tmp_sent++;
      } catch (Swift_BadResponseException $e) {
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
      }
    }

    if ($failed == (count($to) + count($cc)))
    {
      $this->reset();
      $this->notifyListeners($send_event, "SendListener");
      return 0;
    }

    if (!($has_to = $message->getTo()) && !empty($to)) $message->setTo($to);
    if (!($has_cc = $message->getCc()) && !empty($cc)) $message->setCc($cc);

     $this->command("DATA", 354);
    $data = $message->build();

    while (false !== $bytes = $data->read())
    $this->command($bytes, -1);
    if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("<MESSAGE DATA>", Swift_Log::COMMAND);
    try {
      $this->command("\r\n.", 250);
      $sent += $tmp_sent;
    } catch (Swift_BadResponseException $e) {
      $failed += $tmp_sent;
    }/**/

    $tmp_sent = 0;
    $has_bcc = $message->getBcc();
    $it = $list->getIterator("bcc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      if (!$has_bcc) $message->setBcc($address->build());
      try {
        $this->command("MAIL FROM: " . $message->getReturnPath(true), 250);
        $this->command("RCPT TO: " . $address->build(true), 250);
        $this->command("DATA", 354);
        $data = $message->build();
        while (false !== $bytes = $data->read())
        $this->command($bytes, -1);
        if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("<MESSAGE DATA>", Swift_Log::COMMAND);
        $this->command("\r\n.", 250);
        $sent++;
      } catch (Swift_BadResponseException $e) {
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
        $this->reset();
      }
    }

    $total = count($to) + count($cc) + count($list->getBcc());

    $send_event->setNumSent($sent);
    // $this->notifyListeners($send_event, "SendListener");

    if (!$has_return_path) $message->setReturnPath("");
    if (!$has_from) $message->setFrom("");
    if (!$has_to) $message->setTo("");
    if (!$has_reply_to) $message->setReplyTo(null);
    if (!$has_cc) $message->setCc(null);
    if (!$has_bcc) $message->setBcc(null);
    if (!$has_message_id) $message->setId(null);

    if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("Message sent to " . $sent . "/" . $total . " recipients", Swift_Log::NORMAL);

    return $sent;
  }

}