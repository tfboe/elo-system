SELECT
  p1.*,
  p2.*
FROM `elo_players` AS p1
  INNER JOIN elo_players AS p2
    ON p1.id < p2.id AND p2.first_name SOUNDS LIKE p1.first_name AND p2.last_name SOUNDS LIKE p1.last_name
WHERE p1.merged_into_id IS NULL AND p2.merged_into_id IS NULL
ORDER BY `p1`.`id` ASC