<?php
/**
 * This is a simple phone validator class.
 * The task of validating phone numbers for all the countries is more than I can chew. In fact, such a validator could
 * probably never be complete and accurate across time. Regarding the task before us see the following couple of resources:
 * 1. http://stackoverflow.com/questions/123559/a-comprehensive-regex-for-phone-number-validation
 * 2. http://blog.stevenlevithan.com/archives/validate-phone-number
 *
 * This validator is simple and does not attempt to address the problem head on. Instead, in light of 'KISS', it prefers
 * the following simple algorithm:
 *
 * - strip all non-digit characters, whoever, wherever they are. The resulting string should contain numbers only.
 * - the string length should be: $minNumDigits < length < $maxNumDigits. If true - valid. if false - invalid.
 * - if you have more simple rules to add to the second step above please your suggestions.
 *
 * @license:
 * Copyright (c) 2012, Boaz Rymland
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 * - Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 *      disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following
 *      disclaimer in the documentation and/or other materials provided with the distribution.
 * - The names of the contributors may not be used to endorse or promote products derived from this software without
 *      specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
 * TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
class PcSimplePhoneValidator extends CValidator {
	/* @var int $minNumDigits minimum allowed number of digits in the phone number */
	public $minNumDigits = 7;

	/* @var int $minNumDigits maximum allowed number of digits in the phone number */
	public $maxNumDigits = 18;

	/* @var bool $allowEmpty Whether the attribute is allowed to be empty. */
	public $allowEmpty = false;
	/* @var string $message default error message.
	 * Note that if you wish it to be translated please pass translated value to this validator class in rules() method
	 * for the relevant AR class. */
	public $message = "Invalid phone number";

	/* @var string $emptyMessage the message to be displayed if an empty value is validated while 'allowEmpty' is false */
	public $emptyMessage = "{attribute} cannot be blank";
	/* @var bool $logValidationErrors whether to log validation errors or not. When logging is enabled, the log message
	 *     includes the invalid value. I wasn't sure about possible security implications of this so this is by default false. */
	public $logValidationErrors = false;

	/**
	 * validates $attribute in $object.
	 *
	 * @param CModel $object the object to check
	 * @param string $attribute the attribute name to validate in the given $object.
	 *
	 * @throws CException
	 */
	protected function validateAttribute($object, $attribute) {
		// first, if 'allowEmpty' is true and the attribute is indeed empty - finish execution - all good!
		if (empty($object->$attribute)) {
			if ($this->allowEmpty) {
				return;
			}
			$translated_msg = Yii::t("PcSimplePhoneValidator.general", $this->emptyMessage, array('{attribute}' => $attribute));
			$this->addError($object, $attribute, $translated_msg);
			return;
		}
		/*
		 * strip down anything that is not a digit.
		 * at the end, we should be left with number of digits that is no less than minNumDigits and no
		 * more than maxNumDigits.
		 */
		$stripped = mb_ereg_replace('\D', "", $object->$attribute);

		if ((strlen($stripped) > $this->minNumDigits) && (strlen($stripped) < $this->maxNumDigits)) {
			// valid!
			return;
		}

		// not valid
		if ($this->logValidationErrors) {
			Yii::log("phone number in object of type " . get_class($object) . ", as checked in attribute named $attribute, was found to be invalid." .
					" Value supplied = " . $object->$attribute, CLogger::LEVEL_INFO, __METHOD__);
		}
		$translated_msg = Yii::t("PcSimplePhoneValidator.general", $this->message);
		$this->addError($object, $attribute, $translated_msg);
	}
}
