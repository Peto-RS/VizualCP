CREATE TABLE IF NOT EXISTS tenant (
    id INTEGER PRIMARY KEY,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1)),
    created_timestamp TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now'))
);

INSERT INTO tenant (slug, name)
VALUES ('stolarstvo-sucansky', 'Stolárstvo Sučanský')
ON CONFLICT(slug) DO NOTHING;

CREATE TABLE IF NOT EXISTS price_offer_sequence_number (
    tenant_slug TEXT NOT NULL,
    door_type TEXT NOT NULL CHECK (door_type IN ('interior', 'security')),
    value INTEGER NOT NULL DEFAULT 0 CHECK (value >= 0),
    PRIMARY KEY (tenant_slug, door_type),
    FOREIGN KEY (tenant_slug) REFERENCES tenant(slug)
);

INSERT OR IGNORE INTO price_offer_sequence_number (tenant_slug, door_type, value)
VALUES ('stolarstvo-sucansky', 'interior', 0);

INSERT OR IGNORE INTO price_offer_sequence_number (tenant_slug, door_type, value)
VALUES ('stolarstvo-sucansky', 'security', 0);

CREATE TABLE IF NOT EXISTS error_log (
    id INTEGER PRIMARY KEY,
    created_timestamp TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now')),
    error_code TEXT NOT NULL,
    message TEXT NOT NULL,
    stack_trace TEXT,
    session_ref TEXT,
    route TEXT,
    error_type TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS audit_log (
    id INTEGER PRIMARY KEY,
    created_timestamp TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now')),
    actor_type TEXT NOT NULL,
    action TEXT NOT NULL,
    resource_type TEXT NOT NULL,
    resource_id TEXT,
    session_ref TEXT,
    route TEXT,
    details TEXT
);
