-- A1: "Makina fren balata kontrolü" mükerrer custom checklist maddelerini siler.
-- Aynı şirket + aynı bölüm + aynı başlıkta birden fazla satır varsa en eski (min id) kalır.

DELETE c1
FROM `custom_checklist_items` c1
INNER JOIN `custom_checklist_items` c2
    ON c1.company_id = c2.company_id
    AND c1.section_id = c2.section_id
    AND c1.title = c2.title
    AND c1.id > c2.id
WHERE c1.section_id = 'machine_room'
  AND c1.title LIKE '%Makina fren balata%';
