import 'connectivity_service.dart';
import 'offline_storage.dart';
import 'api_services.dart';
import 'package:flutter/material.dart';

class SyncService {
  // Try to sync all pending scans
  static Future<SyncResult> syncPendingScans() async {
    final isConnected = await ConnectivityService.isConnected();

    if (!isConnected) {
      return SyncResult(
        success: false,
        message: 'No internet connection',
        syncedCount: 0,
      );
    }

    final pendingScans = await OfflineStorage.getPendingScans();

    if (pendingScans.isEmpty) {
      return SyncResult(
        success: true,
        message: 'Nothing to sync',
        syncedCount: 0,
      );
    }

    int syncedCount = 0;

    for (final scan in pendingScans) {
      try {
        final result = await ApiService.createGarment({
          'name':       scan['name'] ?? 'Unknown',
          'brand':      scan['brand'] ?? '',
          'category':   scan['category'] ?? '',
          'size_label': scan['size_label'] ?? '',
          'chest':      scan['chest'] ?? '',
          'waist':      scan['waist'] ?? '',
          'length':     scan['length'] ?? '',
          'shoulder':   scan['shoulder'] ?? '',
          'sleeve':     scan['sleeve'] ?? '',
          'status':     'completed',
        });

        if (result['success']) {
          await OfflineStorage.markAsUploaded(scan['id'] as int);
          syncedCount++;
        }
      } catch (e) {
        debugPrint('Sync error for scan ${scan['id']}: $e');
      }
    }

    await OfflineStorage.clearUploaded();

    return SyncResult(
      success: true,
      message: 'Synced $syncedCount garments',
      syncedCount: syncedCount,
    );
  }
}

class SyncResult {
  final bool success;
  final String message;
  final int syncedCount;

  SyncResult({
    required this.success,
    required this.message,
    required this.syncedCount,
  });
}