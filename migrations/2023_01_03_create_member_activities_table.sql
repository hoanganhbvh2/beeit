-- migrations/2023_01_03_create_member_activities_table.sql
CREATE TABLE member_activities (
    member_id INT NOT NULL,
    activity_id INT NOT NULL,
    participation_status ENUM('tham_gia', 'khong_tham_gia') DEFAULT 'khong_tham_gia',
    PRIMARY KEY (member_id, activity_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
