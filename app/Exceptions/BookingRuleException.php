<?php

namespace App\Exceptions;

use RuntimeException;

/** Pelanggaran aturan bisnis yang pesannya aman ditampilkan ke pengguna. */
class BookingRuleException extends RuntimeException {}
