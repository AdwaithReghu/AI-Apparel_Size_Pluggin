import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class OfflineStorage {
  static Database? _database;

  // Initialize database
  static Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  static Future<Database> _initDatabase() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'garment_scanner.db');

    return openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        // Create pending scans table
        await db.execute('''
          CREATE TABLE pending_scans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            brand TEXT,
            category TEXT,
            size_label TEXT,
            chest TEXT,
            waist TEXT,
            length TEXT,
            shoulder TEXT,
            sleeve TEXT,
            status TEXT DEFAULT 'pending_upload',
            image_path TEXT,
            created_at TEXT
          )
        ''');
      },
    );
  }

  // Save scan locally when no internet
  static Future<int> savePendingScan(
      Map<String, dynamic> garmentData) async {
    final db = await database;
    garmentData['created_at'] = DateTime.now().toIso8601String();
    garmentData['status'] = 'pending_upload';
    return await db.insert('pending_scans', garmentData);
  }

  // Get all pending scans
  static Future<List<Map<String, dynamic>>> getPendingScans() async {
    final db = await database;
    return await db.query(
      'pending_scans',
      where: 'status = ?',
      whereArgs: ['pending_upload'],
    );
  }

  // Mark scan as uploaded
  static Future<void> markAsUploaded(int id) async {
    final db = await database;
    await db.update(
      'pending_scans',
      {'status': 'uploaded'},
      where: 'id = ?',
      whereArgs: [id],
    );
  }

  // Get pending count
  static Future<int> getPendingCount() async {
    final db = await database;
    final result = await db.rawQuery(
      'SELECT COUNT(*) as count FROM pending_scans WHERE status = ?',
      ['pending_upload'],
    );
    return result.first['count'] as int;
  }

  // Delete uploaded scans
  static Future<void> clearUploaded() async {
    final db = await database;
    await db.delete(
      'pending_scans',
      where: 'status = ?',
      whereArgs: ['uploaded'],
    );
  }
}