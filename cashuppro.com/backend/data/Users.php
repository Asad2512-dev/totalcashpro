<?php
// backend/data/Users.php

class Users
{
  private Database $db;
  private $ctx = []; // reusable context for lazy loading

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  #region Context
  // Context‑aware getters (lazy loading)
  private function get(int $userId): ?array
  {
    if (!isset($this->ctx['user'])) {
      $this->ctx['user'] = $this->db->fetchOne(
        "SELECT id, `name`, `email`, `password`, question_time_limit, 
          max_people, joined_people, first_started_at, created_at, users__id
          FROM users
          WHERE id = ?",
        [$userId]
      );
    }
    return $this->ctx['user'] ?? null;
  }
  #endregion

  #region Public API
  public function getUserById(int $userId): ?array
  {
    return $this->get($userId);
  }

  public function createUser(string $name, string $email, string $password, string $phone): int
  {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    return $this->db->insert(
      "INSERT INTO users (`name`, `email`, `password`, `phone`, `status`)
        VALUES (?, ?, ?, ?, ?)",
      [$name, $email, $hashedPassword, $phone, 'active']
    );
  }

  public function authenticate(string $email, string $password): ?array
  {
    $user = $this->db->fetchOne(
      "SELECT id, `name`, `email`, `password`, `phone`, `status`
        FROM users
        WHERE email = ?",
      [$email]
    );
    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }
    return null;
  }
  #endregion
}